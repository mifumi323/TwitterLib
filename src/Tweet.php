<?php

namespace MifuminLib\TwitterLib;

/**
 * MifuminLib\TwitterLib\Tweet
 *
 * ツイート一つを表します。
 */
class Tweet
{
    /**
     * Twitterオブジェクト
     *
     * @var \MifuminLib\Twitter
     */
    public $twitter = null;

    /**
     * ツイート内容
     *
     * @var string
     */
    public $status = '';

    /**
     * Tweetクラスで処理できなかった値
     *
     * @var object[]
     */
    public $unknown_values = [];

    /**
     * 初期化します。
     * 通常は、\TGWS\TwitterオブジェクトのprepareTweetメソッドから生成してください。
     *
     * @param \TGWS\Twitter|null $twitter
     */
    public function __construct($twitter = null)
    {
        $this->twitter = $twitter;
    }

    /**
     * ツイートを送信します。
     *
     * @return object
     */
    public function post()
    {
        return $this->twitter->post('statuses/update', $this->toOAuthParams());
    }

    /**
     * ツイート内容を設定します。
     *
     * @param  string              $status
     * @return \TGWS\Twitter\Tweet
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * このオブジェクトをOAuthで送信するパラメータに変換します。
     *
     * @return array
     */
    public function toOAuthParams()
    {
        $params = ['status' => $this->status];

        return $params;
    }
}
