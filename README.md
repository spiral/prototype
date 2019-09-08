Automatic dependency injection using AST
================================
[![Latest Stable Version](https://poser.pugx.org/spiral/prototype/version)](https://packagist.org/packages/spiral/prototype)
[![Build Status](https://travis-ci.org/spiral/prototype.svg?branch=master)](https://travis-ci.org/spiral/prototype)
[![Codecov](https://codecov.io/gh/spiral/prototype/branch/master/graph/badge.svg)](https://codecov.io/gh/spiral/prototype/)

This extension enables [IDE friendly helpers](https://user-images.githubusercontent.com/796136/64488784-a04d0a00-d254-11e9-8650-6a25c71bf46c.png) and let's you to convert this ...

```php
namespace App\Controller;

use Spiral\Prototype\Traits\PrototypeTrait;

class HomeController
{
    use PrototypeTrait;

    public function index()
    {
        $select = $this->users->select();
    }
}
```

... into that via one console command:

```php
namespace App\Controller;

use App\UserRepository;

class HomeController
{
    /** @var UserRepository */
    private $users;

    /**
     * @param UserRepository $users
     */
    public function __construct(UserRepository $users)
    {
        $this->users = $users;
    }

    public function index()
    {
        $select = $this->users->select();
    }
}
```

```bash
$ php app.php proto:inject -r
```
