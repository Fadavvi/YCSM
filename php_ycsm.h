#ifndef PHP_YCSM_H
# define PHP_YCSM_H

extern zend_module_entry ycsm_module_entry;
# define phpext_ycsm_ptr &ycsm_module_entry

#define PHP_YCSM_EXTNAME "ycsm"
#define PHP_YCSM_VERSION "0.013"

PHP_FUNCTION(ycsm_run);

# if defined(ZTS) && defined(COMPILE_DL_YCSM)
ZEND_TSRMLS_CACHE_EXTERN()
# endif

#endif

