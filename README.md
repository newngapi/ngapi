# NG API PHP SDK

A PHP SDK for integrating with the NG gaming platform API. This SDK provides a simple and efficient way to interact with the NG gaming platform's API services.

## Official Resources

- NG Official Website: [https://www.neapi.com](https://www.neapi.com)
- NG Documentation: [https://wiki.neapi.com](https://wiki.neapi.com)
- Contact Email: [newngapi@gmail.com](mailto:newngapi@gmail.com)

## Requirements

- PHP >= 7.0
- Composer
- Guzzle HTTP Client

## Installation

```bash
composer require newngapi/ngapi
```

## Usage

```php
use Newngapi\Ngapi\Client;

// Initialize the client
$client = new Client(
    'https://api.example.com', // Base URL
    'your_sn',                // SN from NG platform
    'your_secret_key'        // Secret key from NG platform
);

// Create a player
$result = $client->createPlayer('ag', 'player123', 'CNY');

// Get player balance
$balance = $client->getBalance('ag', 'player123', 'CNY');

// Get game URL
$gameUrl = $client->getGameUrl(
    'ag',
    'player123',
    '1',  // gameType: 1=视讯, 2=老虎机, 3=彩票, 4=体育, 5=电竞, 6=捕鱼, 7=棋牌
    'CNY',
    'device1'  // device1=电脑网页版, device2=手机网页版
);

// Transfer funds
$transfer = $client->transfer(
    'ag',
    'player123',
    'CNY',
    '100.00',
    '1',  // 1=转入, 2=转出
    'order1234567890123456'  // Optional order ID
);
```

## Development

### Installation

```bash
git clone https://github.com/newngapi/ngapi.git
cd ngapi
composer install
```

### Testing

```bash
./vendor/bin/phpunit
```

### Code Style

This project follows PSR-4 autoloading standard and PSR-12 coding style.

## Available Methods

1. `createPlayer(string $platType, string $playerId, string $currency)`
2. `getBalance(string $platType, string $playerId, string $currency)`
3. `getAllBalance(string $playerId, string $currency)`
4. `transferAll(string $playerId, string $currency)`
5. `getGameUrl(string $platType, string $playerId, string $gameType, string $currency, string $ingress, ?string $lang = null, ?string $gameCode = null, ?string $returnUrl = null, ?string $oddsType = null)`
6. `getDemoUrl(string $platType, string $playerId, string $gameType, string $currency, string $ingress, ?string $lang = null, ?string $gameCode = null, ?string $returnUrl = null)`
7. `getRealTimeRecords(string $currency, ?string $pageNo = null, ?string $pageSize = null)`
8. `getOrderRecords(string $currency, string $orderIds)`
9. `getHistoryRecords(string $currency, string $startTime, string $endTime, ?string $pageNo = null, ?string $pageSize = null)`
10. `transfer(string $platType, string $playerId, string $currency, string $amount, string $type, ?string $orderId = null)`
11. `getTransferStatus(string $playerId, string $currency, string $orderId)`
12. `getGameCode(string $platType)`
13. `getQuota()`

## Error Handling

The SDK will throw `InvalidArgumentException` for invalid parameters and return error responses in the following format:

```php
[
    'code' => 10001,  // Error code
    'msg' => 'Error message',
    'data' => null
]
```

Common error codes:
- 10000: Success
- 10001: Request error
- 10002: Player ID already exists
- 10003: Player ID not exists
- 10004: Player ID format error
- 10005: Transfer error
- 10006: Amount error
- 10007: Time format error
- 10008: Return URL error
- 10009: Frequent interface requests
- 10010: Game not support demo
- 10011: Order ID format error
- 10012: Order ID already exists
- 10013: Order ID not exists
- 10014: Insufficient quota
- 10015: Insufficient merchant balance
- 10403: IP restricted access
- 10404: Signature verification failed
- 10405: Missing parameter
- 10406: Too many parameters
- 10407: Platform code error
- 10408: Game type error
- 10409: Transfer type error

## Support

For technical support or questions, please contact:
- Email: [newngapi@gmail.com](mailto:newngapi@gmail.com)
- Documentation: [https://wiki.neapi.com](https://wiki.neapi.com)

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

MIT