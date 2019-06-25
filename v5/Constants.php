<?php
/**********************************************************************************************
 * 
 * @File: contains constants
 * 
 * @Author: Alexander Bassov
 * @Email: alexander.bassov@trentmann.com
 * 
**********************************************************************************************/

# general information
const TEMPLAX_VERSION = "5.0.0";

# extracts x regardless of spaces # String: "{{ x }}"
const TEMPLAX_PARSING_REGEX_EXTRACT_RULE = "/{{\s*(?:[^<>])*?\s*}}/";

# extracts x regardless of spaces # String: "X[: Y]"
const TEMPLAX_PARSING_REGEX_EXTRACT_REQUEST = "/([\w-]+)(?:[\w\s:-]+)?/";

# extracts x regardless of spaces # String: "[Y]: X"
const TEMPLAX_PARSING_REGEX_EXTRACT_KEY = "/:\s*([\w-]+)?/";