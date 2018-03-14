<?php

namespace MifuminLib\TwitterLib;

/**
 * MifuminLib\TwitterLib\Twitter
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
     * DELETEリクエストを送信します。
     *
     * @param  string       $url
     * @param  array        $params
     * @return object|array
     */
    public function delete($url, $params = [])
    {
        return $this->getTwitterOAuth()->delete($url, $params);
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
     * @param  string   $search
     * @return int|null 成功すればUNIXタイムスタンプ、失敗すればnull
     */
    public function getSearchUpdatedTimestamp($search)
    {
        $result = $this->get('search/tweets', [
            'q' => $search,
            'result_type' => 'recent',
        ]);
        $statuses = $result->statuses;
        if (count($statuses) === 0) {
            return null;
        }
        foreach ($statuses as $line) {
            if (!isset($line->retweeted_status)) {
                return strtotime($line->created_at);
            }
        }

        return null;
    }

    /**
     * TwitterOAuthオブジェクトを返します。
     *
     * @return \Abraham\TwitterOAuth\TwitterOAuth
     */
    public function getTwitterOAuth()
    {
        if (!isset($this->twitteroauth)) {
            $this->twitteroauth = new \Abraham\TwitterOAuth\TwitterOAuth($this->consumer_key, $this->consumer_secret, $this->access_token, $this->access_secret);
        }

        return $this->twitteroauth;
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
     * @param  string                    $status
     * @return \MifuminLib\Twitter\Tweet
     */
    public function prepareTweet($status = '')
    {
        return (new \MifuminLib\TwitterLib\Tweet($this))->setStatus($status);
    }
}
