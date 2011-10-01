The file name for each class should be the error for which you want the class to load.

For example, 404.php is called on a 404 error (though you will need to call it manually, 404 errors are redirected to index.php)

However, the class name must be prepended by error

For example, 404.php would contain class error404.
The class error404 must also extend Application