<?php

namespace Cyclonecode\NewsDataIO\Enums;

abstract class ExceptionCode
{
    const CODE_TOO_MANY_QUERY_FILTER = 'TooManyQueryFilter';
    const CODE_UNSUPPORTED_FILTER = 'UnsupportedFilter';
    const CODE_UNSUPPORTED_PARAMETER = 'UnsupportedParameter';
    const CODE_ACCESS_DENIED = 'AccessDenied';
    const CODE_FILTER_LIMIT_EXCEED = 'FilterLimitExceed';
    const CODE_UNSUPPORTED_QUERY_LENGTH = 'UnsupportedQueryLength';
}
