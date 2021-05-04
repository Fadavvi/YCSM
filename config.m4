PHP_ARG_ENABLE(ycsm, whether to enable YCSM extension, [  --enable-cysm   Enable Rlyeh extension])

if test "$PHP_YCSM" != "no"; then
  PHP_NEW_EXTENSION(ycsm, php_ycsm.c, $ext_shared)
  case $build_os in
  darwin1*.*.*)
    AC_MSG_CHECKING([whether to compile for recent osx architectures])
    CFLAGS="$CFLAGS -arch i386 -arch x86_64"
    AC_MSG_RESULT([yes])
    ;;
  darwin*)
    AC_MSG_CHECKING([whether to compile for every osx architecture ever])
    CFLAGS="$CFLAGS -arch i386 -arch x86_64 -arch ppc -arch ppc64"
    AC_MSG_RESULT([yes])
    ;;
  esac

fi