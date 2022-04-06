<?php
/**
 * management class of Twitch API permission scopes
 *
 * MIT License
 *
 * Copyright (c) 2022 Souzen Yurama
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * TWITCH, the TWITCH Logo, the Glitch Logo, and/or TWITCHTV are trademarks of Twitch Interactive, Inc. or its affiliates.
 * https://www.twitch.tv/p/ja-jp/legal/trademark/
 *
 * official docs: https://dev.twitch.tv/docs/authentication/#scopes
 */
class Scopes
{
    /**
     * @var array scopes(permission) for api
     */
    private array $scopes = [
        'analytics' => [
            'read' => [
                'extensions' => false,
                'games'      => false,
            ],
        ],
        'bits' => [
            'read' => false,
        ],
        'channel' => [
            'edit' => [
                'commercial' => false,
            ],
            'manage' => [
                'broadcast' => false,
                'extensions' => false,
                'polls' => false,
                'predictions' => false,
                'redemptions' => false,
                'schedule' => false,
                'videos' => false,
            ],
            'read' => [
                'editors' => false,
                'goals' => false,
                'hype_train' => false,
                'polls' => false,
                'predictions' => false,
                'redemptions' => false,
                'stream_key' => false,
                'subscriptions' => false,
            ],
            'moderate' => false,
        ],
        'clips' => [
            'edit' => false,
        ],
        'moderation' => [
            'read' => false,
        ],
        'moderator' => [
            'manage' => [
                'banned_users' => false,
            ],
            'moderator' => [
                'read' => [
                    'blocked_terms' => false,
                    'automod_settings' => false,
                    'chat_settings' => false,
                ],
                'manage' => [
                    'blocked_terms' => false,
                    'automod' => false,
                    'automod_settings' => false,
                    'chat_settings' => false,
                ],
            ],
        ],
        'user' => [
            'edit' => [
                false,
                'follows' => false,
            ],
            'manage' => [
                'blocked_users' => false,
            ],
            'read' => [
                'blocked_users' => false,
                'broadcast' => false,
                'email' => false,
                'follows' => false,
                'subscriptions' => false,
            ],
        ],
        'chat' => [
            'edit' => false,
            'read' => false,
        ],
        'whispers' => [
            'edit' => false,
            'read' => false,
        ],
    ];

    /**
     * @var array ":" separated scopeKeys ...looks like "analytics:read:extensions"
     */
    private array $scopeKeys = [];

    /**
     * return all of scopes
     *
     * @param bool|null $isAllowed
     * @return array
     */
    public function getScopes(?bool $isAllowed = null) : array {
        $this->scopeKeys = [];

        $this->parse($this->scopes, '', $isAllowed);

        return $this->scopeKeys;
    }

    /**
     * set scope status
     *
     * @param string $keyString
     * @param bool $isAllow
     * @return void
     * @throws \Exception
     */
    public function setMod(string $keyString, bool $isAllow) : void {
        $keys = explode(':', $keyString);

        $scopes = &$this->scopes;

        foreach($keys as $key) {
            if(!isset($scopes[$key])){
                throw new \Exception(sprintf('scopeKey "%s" is not found. %s is not matched in list. if this not your fail(not implemented in class), please report.', $keyString, $key));
            }

            if(is_bool($scopes[$key])) {
                $scopes[$key] = $isAllow;
            } else {
                $scopes = &$scopes[$key];
            }
        }

        unset($scopes);
    }

    /**
     * set scope status
     *
     * @param array $keyStrings
     * @param bool $isAllow
     * @return void
     * @throws \Exception
     */
    public function setMods(array $keyStrings, bool $isAllow) : void {
        foreach($keyStrings as $keyString) {
            $this->setMod($keyString, $isAllow);
        }
    }

    /**
     * parse and picking up scopeKeys for output
     *
     * @param array $scopes
     * @param string $scopeKey
     * @param bool|null $isAllow status for picking up or not
     * @return void
     */
    public function parse(array $scopes, string $scopeKey = '', ?bool $isAllow = null) :void {
        foreach($scopes as $key => $value) {
            // if $value is bool, then it's as allowed scope or not.
            if (is_bool($value)) {
                // if $isAllowed is null, pick all. if not, then picking up specific ones.
                if( empty($isAllow) || $value === $isAllow ) {
                    if (is_numeric($key)) {
                        // seems $scopeKey is not empty
                        $this->scopeKeys[] = $scopeKey;
                    } else {
                        $this->scopeKeys[] = empty($scopeKey) ? $key : ($scopeKey . ':' . $key);
                    }
                }
            } else {
                $this->parse($value, empty($scopeKey) ? $key : ($scopeKey . ':' . $key), $isAllow);
            }
        }
    }

    /**
     * return toString Space-separated scopeKeys for making API params
     * @return string
     */
    public function __toString() : string {
        $this->scopeKeys = [];

        // only marked "true" scope params.
        $this->parse($this->scopes, '', true);

        return implode(' ', $this->scopeKeys);
    }
}
