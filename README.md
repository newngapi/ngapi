# NG API PHP SDK

NG游戏平台API的PHP SDK。此SDK提供了一种简单高效的方式来与NG游戏平台的API服务进行交互。

## 官方资源

- NG官方网站: [https://www.neapi.com](https://www.neapi.com)
- NG文档: [https://wiki.neapi.com](https://wiki.neapi.com)
- 联系邮箱: [newngapi@gmail.com](mailto:newngapi@gmail.com)

## 系统要求

- PHP >= 7.0
- Composer
- Guzzle HTTP客户端

## 安装

```bash
composer require newngapi/ngapi
```

## 使用方法

```php
use Newngapi\Ngapi\Client;

// 初始化客户端
$client = new Client(
    'https://api.example.com', // 基础URL
    'your_sn',                // NG平台的SN
    'your_secret_key'        // NG平台的密钥
);

// 创建玩家
$result = $client->createPlayer('ag', 'player123', 'CNY');

// 获取玩家余额
$balance = $client->getBalance('ag', 'player123', 'CNY');

// 获取游戏URL
$gameUrl = $client->getGameUrl(
    'ag',
    'player123',
    '1',  // 游戏类型: 1=视讯, 2=老虎机, 3=彩票, 4=体育, 5=电竞, 6=捕鱼, 7=棋牌
    'CNY',
    'device1'  // device1=电脑网页版, device2=手机网页版
);

// 资金转账
$transfer = $client->transfer(
    'ag',
    'player123',
    'CNY',
    '100.00',
    '1',  // 1=转入, 2=转出
    'order1234567890123456'  // 可选的订单ID
);
```

## 开发

### 安装

```bash
git clone https://github.com/newngapi/ngapi.git
cd ngapi
composer install
```

### 测试

```bash
./vendor/bin/phpunit
```

### 代码规范

本项目遵循PSR-4自动加载标准和PSR-12编码风格。

## 可用方法

1. `createPlayer(string $platType, string $playerId, string $currency)` - 创建玩家
2. `getBalance(string $platType, string $playerId, string $currency)` - 获取玩家余额
3. `getAllBalance(string $playerId, string $currency)` - 获取所有平台余额
4. `transferAll(string $playerId, string $currency)` - 全部转账
5. `getGameUrl(string $platType, string $playerId, string $gameType, string $currency, string $ingress, ?string $lang = null, ?string $gameCode = null, ?string $returnUrl = null, ?string $oddsType = null)` - 获取游戏URL
6. `getDemoUrl(string $platType, string $playerId, string $gameType, string $currency, string $ingress, ?string $lang = null, ?string $gameCode = null, ?string $returnUrl = null)` - 获取试玩URL
7. `getRealTimeRecords(string $currency, ?string $pageNo = null, ?string $pageSize = null)` - 获取实时记录
8. `getOrderRecords(string $currency, string $orderIds)` - 获取订单记录
9. `getHistoryRecords(string $currency, string $startTime, string $endTime, ?string $pageNo = null, ?string $pageSize = null)` - 获取历史记录
10. `transfer(string $platType, string $playerId, string $currency, string $amount, string $type, ?string $orderId = null)` - 转账
11. `getTransferStatus(string $playerId, string $currency, string $orderId)` - 获取转账状态
12. `getGameCode(string $platType)` - 获取游戏代码
13. `getQuota()` - 获取配额

## 错误处理

SDK会对无效参数抛出`InvalidArgumentException`异常，并以以下格式返回错误响应：

```php
[
    'code' => 10001,  // 错误代码
    'msg' => '错误信息',
    'data' => null
]
```

### 常见错误代码

- 10000: 成功
- 10001: 请求错误
- 10002: 玩家ID已存在
- 10003: 玩家ID不存在
- 10004: 玩家ID格式错误
- 10005: 转账错误
- 10006: 金额错误
- 10007: 时间格式错误
- 10008: 返回URL错误
- 10009: 接口请求过于频繁
- 10010: 游戏不支持试玩
- 10011: 订单ID格式错误
- 10012: 订单ID已存在
- 10013: 订单ID不存在
- 10014: 配额不足
- 10015: 商户余额不足
- 10403: IP访问受限
- 10404: 签名验证失败
- 10405: 缺少参数
- 10406: 参数过多
- 10407: 平台代码错误
- 10408: 游戏类型错误
- 10409: 转账类型错误

## 游戏类型说明

- 1: 视讯游戏
- 2: 老虎机游戏
- 3: 彩票游戏
- 4: 体育博彩
- 5: 电子竞技
- 6: 捕鱼游戏
- 7: 棋牌游戏

## 设备类型说明

- device1: 电脑网页版
- device2: 手机网页版

## 转账类型说明

- 1: 转入（从平台转入游戏）
- 2: 转出（从游戏转出到平台）

## 技术支持

如需技术支持或有疑问，请联系：
- 邮箱: [newngapi@gmail.com](mailto:newngapi@gmail.com)
- 文档: [https://wiki.neapi.com](https://wiki.neapi.com)

## 贡献

1. Fork本仓库
2. 创建您的功能分支 (`git checkout -b feature/amazing-feature`)
3. 提交您的更改 (`git commit -m 'Add some amazing feature'`)
4. 推送到分支 (`git push origin feature/amazing-feature`)
5. 开启Pull Request

## 许可证

MIT

---

*此文档为中文版本，如需查看英文版本请访问 [README.en.md](README.en.md)*
