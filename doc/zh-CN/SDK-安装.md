# SDK-安装

## 环境需求

SDK 对下列条件有依赖:

- PHP >= 7.0.0
- [scrypt](https://github.com/DomBlack/php-scrypt)
- [libsodium-php](https://github.com/jedisct1/libsodium-php)
- 编译 PHP 时指定 OpenSSL 版本至少为 openssl-1.1.1
- 编译 PHP 时需要开启 gmp 支持
- [php-sm](https://github.com/hsiaosiyuan0/php-sm)

## 环境安装

### PHP 的准备

如果当前的 PHP 编译时链接的 OpenSSL 版本低于 1.1.1，则需要对 PHP 进行重新编译安装。查看当前 PHP 所使用的 OpenSSL 版本的命令为:

```bash
$ php -r "echo OPENSSL_VERSION_TEXT;"
```

当需要重新编译 PHP 时，如果尚未安装高于 1.1.1 版本的 OpenSSL 时，则需要先安装之。查看当前 OpenSSL 版本的命令为:

```bash
$ openssl version
```

对于 OSX 系统，安装 OpenSSL 1.1.1 的命令为:

```bash
brew install openssl@1.1
```

对于 Ubuntu 系统，则可以参考网络上的资料，例如 [Manually Install The Latest OpenSSL Toolkit On Ubuntu 16.04 / 18.04 LTS](https://websiteforstudents.com/manually-install-the-latest-openssl-toolkit-on-ubuntu-16-04-18-04-lts/)

在编译 PHP 时，如果已经安装了所需版本的 OpenSSL，但是 PHP 构建工具链无法默认识别时，则可以通过下面命令指定:

```bash
export PKG_CONFIG_PATH="/usr/local/opt/openssl@1.1/lib/pkgconfig"
```

并且在编译时指定开启 OpenSSL 以及 GMP 支持:

```bash
./configure --with-openssl=/usr/local/opt/openssl@1.1 --with-gmp=/usr/local
```

注意上面的路径需要根据真实的系统情况而定。

### 扩展准备

当 PHP 准备完成后，则可以开始安装剩余的扩展依赖:

- [scrypt](https://github.com/DomBlack/php-scrypt)
- [libsodium-php](https://github.com/jedisct1/libsodium-php)
- [php-sm](https://github.com/hsiaosiyuan0/php-sm)

对于各个扩展的安装方式，都在各自项目的 README 留中有记录。

### 制约条件

当前 SDK 只可以在 OSX 以及 Linux 下运行，因为所依赖的扩展 `php-sm` 尚未释放对应的 Windows 版本。
