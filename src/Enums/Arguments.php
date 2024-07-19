<?php

namespace Cyclonecode\NewsDataIO\Enums;

abstract class Arguments
{
    const MAX_SIZE_FREE = 10;
    const MAX_SIZE_PREMIUM = 50;
    const ARG_APIKEY = 'apikey';
    const ARG_PREMIUM_APIKEY = 'premiumApiKey';
    const ARG_ID = 'id';
    const ARG_COUNTRY = 'country';
    const ARG_LANGUAGE = 'language';
    const ARG_CATEGORY = 'category';
    const ARG_EXCLUDE_CATEGORY = 'excludecategory';
    const ARG_QUERY_TITLE = 'qintitle';
    const ARG_QUERY_META = 'qinmeta';
    const ARG_QUERY = 'q';
    const ARG_SIZE = 'size';
    const ARG_IMAGE = 'image';
    const ARG_VIDEO = 'video';
    const ARG_FULL_CONTENT = 'full_content';
    const ARG_TIMEFRAME = 'timeframe';
    const ARG_REGION = 'region';
    const ARG_SENTIMENT = 'sentiment';
    const ARG_TAG = 'tag';
    const ARG_PAGE = 'page';
    const ARG_TIMEZONE = 'timezone';
    const ARG_DOMAIN = 'domain';
    const ARG_EXCLUDE_DOMAIN = 'excludedomain';
    const ARG_DOMAIN_URL = 'domainurl';
    const ARG_PRIORITY_DOMAIN = 'prioritydomain';
    const ARG_EXCLUDE_FIELD = 'excludefield';
    const ARG_REMOVE_DUPLICATE = 'removeduplicate';
    const ARG_FROM_DATE = 'from_date';
    const ARG_TO_DATE = 'to_date';
    const ARG_COIN = 'coin';
}
