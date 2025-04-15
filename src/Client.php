<?php

namespace Newngapi\Ngapi;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use InvalidArgumentException;

/**
 * NG API Client
 * 
 * This class provides methods to interact with the NG gaming platform API.
 * All API requests require authentication using SN and secret key.
 */
class Client
{
    /** @var string */
    private $baseUrl;
    
    /** @var string */
    private $sn;
    
    /** @var string */
    private $secretKey;
    
    /** @var GuzzleClient */
    private $client;

    /**
     * Client constructor.
     * 
     * @param string $baseUrl API基础URL
     * @param string $sn 商户SN
     * @param string $secretKey 商户密钥
     */
    public function __construct(string $baseUrl, string $sn, string $secretKey)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->sn = $sn;
        $this->secretKey = $secretKey;
        $this->client = new GuzzleClient([
            'timeout' => 30,
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
    }

    private function generateSign(string $random): string
    {
        return md5($random . $this->sn . $this->secretKey);
    }

    private function generateRandom(): string
    {
        $length = rand(16, 32);
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $random = '';
        for ($i = 0; $i < $length; $i++) {
            $random .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $random;
    }

    private function makeRequest(string $endpoint, array $data): array
    {
        $random = $this->generateRandom();
        $sign = $this->generateSign($random);

        try {
            $response = $this->client->post($this->baseUrl . $endpoint, [
                'headers' => [
                    'sign' => $sign,
                    'random' => $random,
                    'sn' => $this->sn,
                    'Content-Type' => 'application/json'
                ],
                'json' => $data
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            return [
                'code' => 10001,
                'msg' => 'Request error: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * 创建玩家账户V2
     * 请求地址：/api/server/create
     * 
     * @param string $platType 游戏平台（参考"游戏平台"附录）
     * @param string $playerId 玩家账号，长度限制 5-11 位 小写字母 + 数字组合
     * @param string $currency 货币代码 BRL:巴西雷亚尔,CNY:人民币,HKD:港币,IDR:印度尼西亚盾,INR:印度卢比,MMK:缅甸元,NGN:尼日利亚奈拉,PHP:菲律宾比索,THB:泰铢,USD:美元,VND:越南盾
     * @return array 返回结果，包含code、msg和data字段
     * @throws InvalidArgumentException 当参数格式不正确时抛出
     */
    public function createPlayer(string $platType, string $playerId, string $currency): array
    {
        if (!preg_match('/^[a-z0-9]{5,11}$/', $playerId)) {
            throw new InvalidArgumentException('Player ID must be 5-11 characters long and contain only lowercase letters and numbers');
        }

        return $this->makeRequest('/api/server/create', [
            'platType' => $platType,
            'playerId' => $playerId,
            'currency' => $currency
        ]);
    }

    /**
     * 查询余额V2
     * 请求地址：/api/server/balance
     * 
     * @param string $platType 游戏平台（参考"游戏平台"附录）
     * @param string $playerId 玩家账号
     * @param string $currency 货币代码 BRL:巴西雷亚尔,CNY:人民币,HKD:港币,IDR:印度尼西亚盾,INR:印度卢比,MMK:缅甸元,NGN:尼日利亚奈拉,PHP:菲律宾比索,THB:泰铢,USD:美元,VND:越南盾
     * @return array 返回结果，包含code、msg和data字段
     */
    public function getBalance(string $platType, string $playerId, string $currency): array
    {
        return $this->makeRequest('/api/server/balance', [
            'platType' => $platType,
            'playerId' => $playerId,
            'currency' => $currency
        ]);
    }

    /**
     * 查询全平台余额V2.0
     * 请求地址：/api/server/balanceAll
     * 请求说明：每个玩家每分钟内请求不能超过 3 次
     * 
     * @param string $playerId 玩家账号
     * @param string $currency 货币代码 BRL:巴西雷亚尔,CNY:人民币,HKD:港币,IDR:印度尼西亚盾,INR:印度卢比,MMK:缅甸元,NGN:尼日利亚奈拉,PHP:菲律宾比索,THB:泰铢,USD:美元,VND:越南盾
     * @return array 返回结果，包含code、msg和data字段
     */
    public function getAllBalance(string $playerId, string $currency): array
    {
        return $this->makeRequest('/api/server/balanceAll', [
            'playerId' => $playerId,
            'currency' => $currency
        ]);
    }

    /**
     * 一键转出全平台额度v2.0
     * 请求地址：/api/server/transferAll
     * 请求说明：
     * - 每个玩家每分钟内请求不能超过 2 次
     * - 请求超时时间必须设置大于 30 秒
     * - 部分游戏平台在游戏中无法转换额度
     * 
     * @param string $playerId 玩家账号
     * @param string $currency 货币代码，CNY:人民币，USD:美元，HKD:港币，PHP:菲律宾比索
     * @return array 返回结果，包含code、msg和data字段
     */
    public function transferAll(string $playerId, string $currency): array
    {
        return $this->makeRequest('/api/server/transferAll', [
            'playerId' => $playerId,
            'currency' => $currency
        ]);
    }

    /**
     * 登录游戏V2
     * 请求地址：/api/server/gameUrl
     * 
     * @param string $platType 游戏平台（参考"游戏平台"附录）
     * @param string $playerId 玩家账号
     * @param string $gameType 游戏类型，（1:视讯、2:老虎机、3:彩票、4:体育、5:电竞、6:捕鱼、7:棋牌）
     * @param string $currency 货币代码 BRL:巴西雷亚尔,CNY:人民币,HKD:港币,IDR:印度尼西亚盾,INR:印度卢比,MMK:缅甸元,NGN:尼日利亚奈拉,PHP:菲律宾比索,THB:泰铢,USD:美元,VND:越南盾
     * @param string $ingress 终端类型，device1:电脑网页版、device2:手机网页版（其他特定终端请参考"游戏平台"附录）
     * @param string|null $lang 游戏语言，默认简体中文（参考"游戏语言"附录）
     * @param string|null $gameCode 游戏代码，默认游戏大厅（参考"游戏平台"附录）
     * @param string|null $returnUrl 游戏退出时跳转地址，示例："https://www.neapi.com"
     * @param string|null $oddsType 彩票盘口，A:(默认)、B、C，仅IG彩票和SGWin彩票可选
     * @return array 返回结果，包含code、msg和data字段
     */
    public function getGameUrl(string $platType, string $playerId, string $gameType, string $currency, string $ingress, ?string $lang = null, ?string $gameCode = null, ?string $returnUrl = null, ?string $oddsType = null): array
    {
        $data = [
            'platType' => $platType,
            'playerId' => $playerId,
            'gameType' => $gameType,
            'currency' => $currency,
            'ingress' => $ingress
        ];

        if ($lang) $data['lang'] = $lang;
        if ($gameCode) $data['gameCode'] = $gameCode;
        if ($returnUrl) $data['returnUrl'] = $returnUrl;
        if ($oddsType) $data['oddsType'] = $oddsType;

        return $this->makeRequest('/api/server/gameUrl', $data);
    }

    /**
     * 试玩游戏V2
     * 请求地址：/api/server/demoUrl
     * 
     * @param string $platType 游戏平台（参考"游戏平台"附录）
     * @param string $playerId 试玩玩家账号
     * @param string $gameType 游戏类型，（1:视讯、2:老虎机、3:彩票、4:体育、5:电竞、6:捕鱼、7:棋牌）
     * @param string $currency 货币代码，CNY:人民币，USD:美元，HKD:港币，PHP:菲律宾比索
     * @param string $ingress 终端类型，device1:电脑网页版、device2:手机网页版（其他特定终端请参考"终端类型"附录）
     * @param string|null $lang 游戏语言，默认简体中文（参考"游戏语言"附录）
     * @param string|null $gameCode 游戏代码，默认游戏大厅（参考"游戏代码"附录）
     * @param string|null $returnUrl 游戏退出时跳转地址，示例："https://www.neapi.com"
     * @return array 返回结果，包含code、msg和data字段
     */
    public function getDemoUrl(string $platType, string $playerId, string $gameType, string $currency, string $ingress, ?string $lang = null, ?string $gameCode = null, ?string $returnUrl = null): array
    {
        $data = [
            'platType' => $platType,
            'playerId' => $playerId,
            'gameType' => $gameType,
            'currency' => $currency,
            'ingress' => $ingress
        ];

        if ($lang) $data['lang'] = $lang;
        if ($gameCode) $data['gameCode'] = $gameCode;
        if ($returnUrl) $data['returnUrl'] = $returnUrl;

        return $this->makeRequest('/api/server/demoUrl', $data);
    }

    /**
     * 实时记录V2
     * 请求地址：/api/server/recordAll
     * 请求说明：
     * - 每分钟请求不能超过 1 次
     * - 每次请求固定返回最新 10 分钟游戏记录（不包含当前分钟）
     * 
     * @param string $currency 游戏货币，参考"游戏平台"附录
     * @param string|null $pageNo 页码，默认第1页，按订单更新时间正序返回数据
     * @param string|null $pageSize 页容量，默认200，最大2000
     * @return array 返回结果，包含code、msg和data字段
     */
    public function getRealTimeRecords(string $currency, ?string $pageNo = null, ?string $pageSize = null): array
    {
        $data = ['currency' => $currency];
        if ($pageNo) $data['pageNo'] = $pageNo;
        if ($pageSize) $data['pageSize'] = $pageSize;

        return $this->makeRequest('/api/server/recordAll', $data);
    }

    /**
     * 投注订单查询V2
     * 请求地址：/api/server/recordOrder
     * 请求说明：每分钟请求不能超过 1 次
     * 
     * @param string $currency 游戏货币，参考"游戏平台"附录
     * @param string $orderIds 订单号，多笔订单号以,分隔
     * @return array 返回结果，包含code、msg和data字段
     */
    public function getOrderRecords(string $currency, string $orderIds): array
    {
        return $this->makeRequest('/api/server/recordOrder', [
            'currency' => $currency,
            'orderIds' => $orderIds
        ]);
    }

    /**
     * 历史记录V2
     * 请求地址：/api/server/recordHistory
     * 请求说明：
     * - 每小时请求不能超过 5 次
     * - 每次请求必须间隔 1 分钟（不包含分页）
     * - 开始时间必须小于或等于结束时间
     * - 开始时间和结束时间不能超过 6 小时（可以获取 15 天内的数据）
     * 
     * @param string $currency 游戏货币，参考"游戏平台"附录
     * @param string $startTime 订单更新时间 UTC +8 开始，时间格式："yyyy-MM-dd HH:mm:ss"，示例："2022-08-08 18:18:18"
     * @param string $endTime 订单更新时间 UTC +8 结束，时间格式："yyyy-MM-dd HH:mm:ss"，示例："2022-08-08 18:18:18"
     * @param string|null $pageNo 页码，默认第1页，按订单更新时间正序返回数据
     * @param string|null $pageSize 页容量，默认200，最大2000
     * @return array 返回结果，包含code、msg和data字段
     */
    public function getHistoryRecords(string $currency, string $startTime, string $endTime, ?string $pageNo = null, ?string $pageSize = null): array
    {
        $data = [
            'currency' => $currency,
            'startTime' => $startTime,
            'endTime' => $endTime
        ];

        if ($pageNo) $data['pageNo'] = $pageNo;
        if ($pageSize) $data['pageSize'] = $pageSize;

        return $this->makeRequest('/api/server/recordHistory', $data);
    }

    /**
     * 额度转换V2
     * 请求地址：/api/server/transfer
     * 请求说明：
     * - 开元棋牌/美天棋牌/乐游棋牌/PG电子在游戏中不能转额度
     * - 非常重要：额度转换请求超时时间必须设置大于30秒
     * 
     * @param string $platType 游戏平台（参考"游戏平台"附录）
     * @param string $playerId 玩家账号
     * @param string $currency 货币代码 BRL:巴西雷亚尔,CNY:人民币,HKD:港币,IDR:印度尼西亚盾,INR:印度卢比,MMK:缅甸元,NGN:尼日利亚奈拉,PHP:菲律宾比索,THB:泰铢,USD:美元,VND:越南盾
     * @param string $amount 转换金额，支持两位小数，最小金额1
     * @param string $type 转换类型，1:转入、2:转出
     * @param string|null $orderId 订单号，16-32 位字母 + 数字组合，商户平台传过来要保证唯一性，示例："97460a6f6eaacc7e246a0a4374b64c13"
     * @return array 返回结果，包含code、msg和data字段
     * @throws InvalidArgumentException 当参数格式不正确时抛出
     */
    public function transfer(string $platType, string $playerId, string $currency, string $amount, string $type, ?string $orderId = null): array
    {
        if (!is_numeric($amount) || $amount < 1) {
            throw new InvalidArgumentException('Amount must be a number greater than or equal to 1');
        }

        if (!in_array($type, ['1', '2'])) {
            throw new InvalidArgumentException('Type must be either 1 (transfer in) or 2 (transfer out)');
        }

        $data = [
            'platType' => $platType,
            'playerId' => $playerId,
            'currency' => $currency,
            'amount' => $amount,
            'type' => $type
        ];

        if ($orderId) {
            if (!preg_match('/^[a-zA-Z0-9]{16,32}$/', $orderId)) {
                throw new InvalidArgumentException('Order ID must be 16-32 characters long and contain only letters and numbers');
            }
            $data['orderId'] = $orderId;
        }

        return $this->makeRequest('/api/server/transfer', $data);
    }

    /**
     * 额度转换状态查询v2
     * 请求地址：/api/server/transferStatus
     * 
     * @param string $playerId 玩家账号
     * @param string $currency 货币代码 BRL:巴西雷亚尔,CNY:人民币,HKD:港币,IDR:印度尼西亚盾,INR:印度卢比,MMK:缅甸元,NGN:尼日利亚奈拉,PHP:菲律宾比索,THB:泰铢,USD:美元,VND:越南盾
     * @param string $orderId 订单号
     * @return array 返回结果，包含code、msg和data字段
     */
    public function getTransferStatus(string $playerId, string $currency, string $orderId): array
    {
        return $this->makeRequest('/api/server/transferStatus', [
            'playerId' => $playerId,
            'currency' => $currency,
            'orderId' => $orderId
        ]);
    }

    /**
     * 获取游戏代码v2
     * 请求地址：/api/server/gameCode
     * 请求说明：每小时请求不能超过 30 次
     * 
     * @param string $platType 游戏平台，目前支持：ag、as、bbin、bg、boya、cq9、db2、db6、db7、fc、fg、jdb、joker、km、ky、leg、lgd、mg、mt、mw、nw、pg、pp、pt、rsg、t1、vg、wl、ww、xgd、yoo
     * @return array 返回结果，包含code、msg和data字段
     */
    public function getGameCode(string $platType): array
    {
        return $this->makeRequest('/api/server/gameCode', [
            'platType' => $platType
        ]);
    }

    /**
     * 查询商户余额V2
     * 请求地址：/api/server/quota
     * 此接口不需要传参数
     * 
     * @return array 返回结果，包含code、msg和data字段
     */
    public function getQuota(): array
    {
        return $this->makeRequest('/api/server/quota', []);
    }
} 