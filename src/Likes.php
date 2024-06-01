<?php

namespace Mifumi323\TwitterLib;

/**
 * いいねの集合を取り扱います。
 */
class Likes
{
    /**
     * Twitterオブジェクト
     *
     * @var \Mifumi323\TwitterLib\Twitter
     */
    public $twitter;

    /**
     * いいねしたツイートのユーザーごとの集合(キー1つ目：ユーザーID、キー2つ目：ツイートID)
     *
     * @var object[][]
     */
    public $likes_by_users = [];

    /**
     * いいねしたユーザーのツイートごとの集合(キー1つ目：ツイートID、キー2つ目：ユーザーID)
     *
     * @var object[][]
     */
    public $likes_of_tweets = [];

    /**
     * 初期化します。
     */
    public function __construct(?Twitter $twitter)
    {
        $this->twitter = $twitter;
    }

    /**
     * いいねしているかどうか取得します。
     */
    public function isLiked(int $user_id, int $tweet_id, bool $force = false): bool
    {
        $this->prepareOfTweet($tweet_id, $force);
        if (isset($this->likes_of_tweets[$tweet_id][$user_id])) {
            return true;
        }

        $this->prepareByUser($user_id, $tweet_id, $force);
        if (isset($this->likes_by_users[$user_id][$tweet_id])) {
            return true;
        }

        return false;
    }

    /**
     * 特定のツイートに対するいいねを情報を準備します。
     */
    public function prepareByUser(int $user_id, int $tweet_id, bool $force = false): void
    {
        if ($force || !isset($this->likes_by_users[$user_id][$tweet_id])) {
            $oauth = $this->twitter->getTwitterOAuth();
            $likes = $oauth->get('favorites/list', [
                'user_id' => $user_id,
                'count' => 1,
                'include_entities' => false,
                'since_id' => $tweet_id - 1,
                'max_id' => $tweet_id,
            ]);
            if (count($likes)) {
                $this->likes_by_users[$user_id][$tweet_id] = $likes[0];
            }
        }
    }

    /**
     * 特定のツイートに対するいいねを情報を準備します。
     */
    public function prepareOfTweet(int $tweet_id, bool $force = false): void
    {
        if ($force || !isset($this->likes_of_tweets[$tweet_id])) {
            $oauth = $this->twitter->getTwitterOAuth();
            $oauth->setApiVersion('2');
            $likes = $oauth->get('tweets/'.$tweet_id.'/liking_users');
            $this->likes_of_tweets[$tweet_id] = [];
            if (isset($likes->data) && is_array($likes->data)) {
                foreach ($likes->data as $like) {
                    $this->likes_of_tweets[$tweet_id][$like->id] = $like;
                }
            }
            $oauth->setApiVersion('1.1');
        }
    }
}
