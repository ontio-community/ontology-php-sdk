# Docker 镜像及使用

- [获取镜像](#获取镜像)

- [镜像内容简介](#镜像内容简介)

- [运行测试用例](#运行测试用例)

## 获取镜像

为了方便快速体验 SDK 功能、同时使得可以快速的进入开发，故提供了 Docker 镜像 [ontiphpsdk](https://hub.docker.com/r/hsiaosiyuan0/ontiphpsdk/)。

通过下面的命令拉取镜像文件:

```bash
docker pull hsiaosiyuan0/ontiphpsdk:0.1
```

通过下面的命令运行镜像:

```bash
docker run -it -p 20334-20336:20334-20336  hsiaosiyuan0/ontiphpsdk:0.1
```

## 镜像内容简介

该镜像中安装了 SDK 所需的运行环境，关于 SDK 所需的运行环境的细节，请参考 [SDK-安装](SDK-安装.md)。

同时该镜像中还安装了 master 版的 [ontology](https://github.com/ontio/ontology)，项目路径位于:

```bash
/home/go/src/github.com/ontio/ontology
```

并且已经生成了测试所用的钱包文件，文件位于:

```bash
/home/go/src/github.com/ontio/ontology/wallet.dat
```

钱包密码为 `123456`

## 运行测试用例

最快速体验 SDK 的方式就是运行其测试用例，SDK 项目位于:

```bash
/home/ontology-php-sdk
```

并且已经使用 `composer install` 安装过项目依赖。

在运行测试用例之前，需要先以 `testmode` 启动本地的 ontology 节点。这是因为测试用例中包含了和节点交互的逻辑。为了简化这一步骤，镜像中提供了启动脚本:

```bash
/home/start-onti.sh
```

该脚本会自动地以 `testmode` 启动一个本地 ontology 节点，并且使用的是上面提到的此时钱包。如果需要查看该本地节点的运行状态，可以通过命令:

```bash
screen -r
```

进行查看，如果此时想回到命令行界面，可以通过组合键 `Ctrl+a d` (先 `Ctrl+a` 再按下 `d`)。

关于 screen 命令的更多细节不在这里继续展开。

另外两个脚本以及各自的功能为:

```bash
# 显示地址 ASSxYHNSsh4FdF2iNvHdh3Np2sgWU21hfp 上未解绑的 ong
/home/show-ong.sh

# 解绑地址 ASSxYHNSsh4FdF2iNvHdh3Np2sgWU21hfp 上的 ong
/home/withdraw-ong.sh
```

`ASSxYHNSsh4FdF2iNvHdh3Np2sgWU21hfp` 为上面提到的测试钱包中的默认账户。

在成功启动了本地节点后，则可以进入 SDK 项目目录运行测试用例:

```bash
cd /home/ontology-php-sdk
composer test
```
