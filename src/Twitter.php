<?php

namespace Mifumi323\TwitterLib;

/**
 * Mifumi323\TwitterLib\Twitter
 *
 * Twitter APIのラッパーです。
 */
class Twitter
{
    public $consumer_key = null;
    public $consumer_secret = null;
    public $access_token = null;
    public $access_secret = null;

    /**
     * TwitterOAuthオブジェクト
     *
     * @var \Abraham\TwitterOAuth\TwitterOAuth
     */
    private $twitteroauth = null;

    /**
     * Likesオブジェクト
     *
     * @var Likes|null
     */
    private $likes = null;

    /**
     * 初期化します。
     *
     * @param string|null $consumer_key
     * @param string|null $consumer_secret
     * @param string|null $access_token
     * @param string|null $access_secret
     */
    public function __construct($consumer_key = null, $consumer_secret = null, $access_token = null, $access_secret = null)
    {
        $this->consumer_key = $consumer_key;
        $this->consumer_secret = $consumer_secret;
        $this->access_token = $access_token;
        $this->access_secret = $access_secret;
    }

    /**
     * ツイートを削除します。
     */
    public function deleteTweet(string $id_str): object
    {
        return $this->post('statuses/destroy/'.$id_str);
    }

    /**
     * GETリクエストを送信します。
     *
     * @param  string       $url
     * @param  array        $params
     * @return object|array
     */
    public function get($url, $params = [])
    {
        return $this->getTwitterOAuth()->get($url, $params);
    }

    /**
     * 検索メモを取得します。
     *
     * @return object[]
     */
    public function getSavedSearches()
    {
        return $this->get('saved_searches/list', []);
    }

    /**
     * 検索結果の最終更新のタイムスタンプを取得します。
     *
     * @param  string        $search    検索キーワード
     * @param  callable|null $condition 検索結果のフィルター条件。1ツイート分のJSONデータを受け取り、bool値を返す(省略可)
     * @return int|null      成功すればUNIXタイムスタンプ、失敗すればnull
     */
    public function getSearchUpdatedTimestamp(string $search, ?callable $condition = null): ?int
    {
        $result = $this->get('search/tweets', [
            'q' => $search,
            'result_type' => 'recent',
            'count' => 100,
            'tweet_mode' => 'extended',
        ]);
        $statuses = $result->statuses ?? [];
        if (count($statuses) === 0) {
            return null;
        }
        foreach ($statuses as $line) {
            if (!isset($line->retweeted_status) && (!is_callable($condition) || $condition($line))) {
                return strtotime($line->created_at);
            }
        }

        return null;
    }

    /**
     * TwitterOAuthオブジェクトを返します。
     */
    public function getTwitterOAuth(): \Abraham\TwitterOAuth\TwitterOAuth
    {
        if (!isset($this->twitteroauth)) {
            $this->twitteroauth = new \Abraham\TwitterOAuth\TwitterOAuth($this->consumer_key, $this->consumer_secret, $this->access_token, $this->access_secret);
        }

        return $this->twitteroauth;
    }

    /**
     * 指定のユーザーが指定のツイートにいいねを付けたかどうかを判定します。
     */
    public function isFavorited(string $screen_name, int $tweet_id)
    {
        $result = $this->get('favorites/list', [
            'screen_name' => $screen_name,
            'count' => 1,
            'include_entities' => false,
            'since_id' => $tweet_id - 1,
            'max_id' => $tweet_id,
        ]);

        return count($result) > 0;
    }

    /**
     * 指定のユーザーが指定のツイートにいいねを付けたかどうかを判定します。
     */
    public function isLiked(int $user_id, int $tweet_id)
    {
        if (!isset($this->likes)) {
            $this->likes = new Likes($this);
        }

        return $this->likes->isLiked($user_id, $tweet_id);
    }

    /**
     * POSTリクエストを送信します。
     *
     * @param  string       $url
     * @param  array        $params
     * @return object|array
     */
    public function post($url, $params = [])
    {
        return $this->getTwitterOAuth()->post($url, $params);
    }

    /**
     * ツイートを送信します。
     *
     * @param  string $status
     * @return object
     */
    public function postTweet($status)
    {
        return $this->prepareTweet($status)->post();
    }

    /**
     * ツイートを送信できるように準備します。
     *
     * @param string $status
     */
    public function prepareTweet($status = ''): Tweet
    {
        return (new \Mifumi323\TwitterLib\Tweet($this))->setStatus($status);
    }

    /**
     * リツイートします。
     *
     * @codeCoverageIgnore
     */
    public function retweet(int $tweet_id): object
    {
        return $this->post('statuses/retweet', ['id' => $tweet_id]);
    }
}
