<?php

namespace LeeOvery\WordpressToLaravel;

use DB;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Collection;
use League\Fractal\Manager as FractalManager;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;
use stdClass;

class WordpressToLaravel
{
    /**
     * @var string
     */
    protected $endpoint = 'wp-json/wp/v2/';

    /**
     * @var FractalManager
     */
    protected $fractalManager;

    /**
     * @var GuzzleClient
     */
    protected $client;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var string
     */
    protected $postModel;

    /**
     * @var string
     */
    protected $categoryModel;

    /**
     * @var string
     */
    protected $authorModel;

    /**
     * @var TransformerAbstract
     */
    protected $postTransformer;

    /**
     * @var TransformerAbstract
     */
    protected $categoryTransformer;

    /**
     * @var TransformerAbstract
     */
    protected $authorTransformer;

    /**
     * @var TransformerAbstract
     */
    protected $tagTransformer;

    /**
     * WordpressToLaravel constructor.
     *
     * @param FractalManager $fractalManager
     * @param GuzzleClient   $client
     * @param array          $config
     */
    public function __construct(FractalManager $fractalManager, GuzzleClient $client, array $config)
    {
        $this->fractalManager = $fractalManager;
        $this->client = $client;
        $this->config = $config;

        $this->setupModels();
        $this->setupTransformers();
    }

    protected function setupModels()
    {
        $this->postModel = $this->config['post_model'] ?? Post::class;
        $this->categoryModel = $this->config['category_model'] ?? Category::class;
        $this->authorModel = $this->config['author_model'] ?? Author::class;
    }

    protected function setupTransformers()
    {
        $this->postTransformer = array_get($this->config, 'transformers.post')
            ?? PostTransformer::class;
        $this->categoryTransformer = array_get($this->config, 'transformers.category')
            ?? CategoryTransformer::class;
        $this->authorTransformer = array_get($this->config, 'transformers.author')
            ?? AuthorTransformer::class;
        $this->tagTransformer = array_get($this->config, 'transformers.tag')
            ?? TagTransformer::class;
    }

    /**
     * @param int  $page
     * @param int  $perPage
     * @param bool $truncate
     */
    public function import($page = 1, $perPage = 5, $truncate = false)
    {
        $this->truncate($truncate)
             ->fetchPosts($page, $perPage)
             ->map(function ($post) {
                 return $this->transformPost($post);
             })
             ->each(function ($post) {
                 $this->syncPost($post);
             });
    }

    /**
     * Setup the getPosts request
     *
     * @param int $page
     * @param     $perPage
     * @return Collection
     */
    protected function fetchPosts($page, $perPage)
    {
        return collect(
            $this->sendRequest(
                $this->makeUrl($page, $perPage)
            )
        );
    }

    /**
     * Send the request
     *
     * @param string $url
     * @return array
     */
    protected function sendRequest($url)
    {
        if ($results = $this->client->get($url)) {
            return json_decode(
                $results->getBody()
            );
        }

        return [];
    }

    /**
     * @param $page
     * @param $perPage
     * @return string
     */
    protected function makeUrl($page, $perPage)
    {
        $queryString = sprintf(
            'posts?_embed&filter[orderby]=modified&page=%d&per_page=%d',
            $page, $perPage
        );

        return sprintf(
            '%s%s%s',
            str_finish($this->config['api_url'], '/'),
            $this->endpoint,
            $queryString
        );
    }

    protected function truncate($truncate)
    {
        if ($truncate) {
            ($this->postModel)::truncate();
            ($this->categoryModel)::truncate();
            ($this->authorModel)::truncate();
            DB::table('tags')->truncate();
            DB::table('tagged')->truncate();
        }

        return $this;
    }

    /**
     * @param stdClass $post
     * @return array
     */
    protected function transformPost(stdClass $post)
    {
        return $this->fractalManager->createData($this->createPostResource($post))
                                    ->toArray();
    }

    /**
     * @param stdClass $post
     * @return Item
     */
    private function createPostResource(stdClass $post): Item
    {
        return new Item($post, new $this->postTransformer(
            $this->authorTransformer,
            $this->categoryTransformer,
            $this->tagTransformer
        ));
    }

    /**
     * @param array $data
     */
    protected function syncPost($data)
    {
        $tagsData = $data['tags'];
        $authorData = $data['author'];
        $categoryData = $data['category'];
        unset($data['tags'], $data['author'], $data['category']);

        if (! $post = ($this->postModel)::where('wp_id', $data['wp_id'])->first()) {
            $post = ($this->postModel)::create($data);
        }

        if ($data['updated_at']->gt($post->updated_at)) {
            $post->update($data);
        }

        if (! is_null($post)) {
            $post->setTags($tagsData);
            $post->setCategory($categoryData);
            $post->setAuthor($authorData);
            $post->save();
        }
    }
}
