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
     * @var \MifuminLib\TwitterLib\Twitter|null
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
     * 通常は、\MifuminLib\TwitterLib\TwitterオブジェクトのprepareTweetメソッドから生成してください。
     *
     * @param \MifuminLib\TwitterLib\Twitter|null $twitter
     */
    public function __construct(?\MifuminLib\TwitterLib\Twitter $twitter = null)
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
     * @return \MifuminLib\TwitterLib\Tweet
     */
    public function setStatus($status): \MifuminLib\TwitterLib\Tweet
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
