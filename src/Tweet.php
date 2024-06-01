<?php

namespace Mifumi323\TwitterLib;

/**
 * Mifumi323\TwitterLib\Tweet
 *
 * ツイート一つを表します。
 */
class Tweet
{
    /**
     * Twitterオブジェクト
     *
     * @var \Mifumi323\TwitterLib\Twitter|null
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
     * 通常は、\Mifumi323\TwitterLib\TwitterオブジェクトのprepareTweetメソッドから生成してください。
     *
     * @param \Mifumi323\TwitterLib\Twitter|null $twitter
     */
    public function __construct(?\Mifumi323\TwitterLib\Twitter $twitter = null)
    {
        $this->twitter = $twitter;
    }

    /**
     * ツイートを送信します。
     *
     * @return object
     */
    public function post(): object
    {
        return $this->twitter->post('statuses/update', $this->toOAuthParams());
    }

    /**
     * ツイート内容を設定します。
     *
     * @param  string                 $status
     * @return \Mifumi323\TwitterLib\Tweet
     */
    public function setStatus($status): \Mifumi323\TwitterLib\Tweet
    {
        $this->status = $status;

        return $this;
    }

    /**
     * このオブジェクトをOAuthで送信するパラメータに変換します。
     *
     * @return array
     */
    public function toOAuthParams(): array
    {
        $params = ['status' => $this->status];

        return $params;
    }
}
