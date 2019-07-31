<h1 align="center"> mouyong/puyingcloud-sdk </h1>

<p align="center"> 普赢云 sdk.</p>

<p align="center">
<a href="https://travis-ci.org/mouyong/puyingcloud-sdk"><img src="https://travis-ci.org/mouyong/puyingcloud-sdk.svg" alt="TravisCI"></a>
<a href="https://github.styleci.io/repos/199642318"><img src="https://github.styleci.io/repos/199642318/shield" alt="StyleCI"></a>
</p>

## Installing

```shell
$ composer require mouyong/puyingcloud-sdk -vvv
```

## Usage

```
use Yan\PuyingCloudSdk\Kernel\ContentFormatter;

require __DIR__.'/vendor/autoload.php';

$sdk = new \Yan\PuyingCloudSdk\PuyingCloudSdk([
    'debug' => true, // 必须有，不然 foundation 72 行会报 Notice 未定义索引 debug 错误
    'phone' => 'your-phone-number',
    'password' => 'your-password',

    'log' => [
        'file' => __DIR__.'/runtime.log',
        'level' => 'debug',
        'permission' => 0777,
    ],

    'cache' => new \Doctrine\Common\Cache\FilesystemCache(__DIR__.'/cache/'),
]);

class TestFormatter extends ContentFormatter implements \Yan\PuyingCloudSdk\Contracts\Formatter {
    public function format()
    {
        $title = $this->title($this->content['title']);

        $this->result = $title;
    }
}

$order = [
    'title' => '123',
    'desc' => '456',
];

try {
    echo json_encode($sdk->printer->createPrintTask($sn, '测试打印', new TestFormatter($order)));
    
    echo json_encode($sdk->printer->getPrinterlist());
} catch (\Exception $e) {
    echo $e->getMessage();
}

```

## Contributing

You can contribute in one of three ways:

1. File bug reports using the [issue tracker](https://github.com/yan/ong/puyingcloud-sdk/issues).
2. Answer questions or fix bugs on the [issue tracker](https://github.com/yan/ong/puyingcloud-sdk/issues).
3. Contribute new features or update the wiki.

_The code contribution process is not very formal. You just need to make sure that you follow the PSR-0, PSR-1, and PSR-2 coding guidelines. Any new code contributions must be accompanied by unit tests where applicable._

## License

MIT