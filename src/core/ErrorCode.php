<?php

namespace ontio\core;

class ErrorCode
{
  // 成功
  const SUCCESS = 0;
  // 会话无效或已过期（ 需要重新登录）
  const SESSION_EXPIRED = 41001;
  // 达到服务上限
  const SERVICE_CEILING = 41002;
  // 不合法数据格式
  const ILLEGAL_DATA_FORMAT = 41003;
  // 不合法的版本
  const INVALID_VERSION = 41004;
  // 无效的方法
  const INVALID_METHOD = 42001;
  // 无效的参数
  const INVALID_PARAMS = 42002;
  // 无效的交易
  const INVALID_TRANSACTION = 43001;
  // 无效的资产
  const INVALID_ASSET = 43002;
  // 无效的块
  const INVALID_BLOCK = 43003;
  // 找不到交易
  const UNKNOWN_TRANSACTION = 44001;
  // 找不到资产
  const UNKNOWN_ASSET = 44002;
  // 找不到块
  const UNKNOWN_BLOCK = 44003;
  // 找不到合约
  const UNKNOWN_CONTRACT = 44004;
  // 内部错误
  const INTERNAL_ERROR = 45001;
  // 智能合约错误
  const SMART_CODE_ERROR = 47001;
  // 不存在的 ONT ID
  const UNKNOWN_ONTID = 51000;
  // 网络错误
  const NETWORK_ERROR = 52000;
  // 解密错误
  const DECRYPT_ERROR = 53000;
  // 地址验证失败
  const INVALID_ADDR = 53001;
  // 预执行错误
  const PreExec_ERROR = 54000;
}
