--- PHP Config Files ---
Must be named the same as the array inside them. E.g. router.php contains the array $config['router']

--- INI Config Files ---
Must only have one section, which has the same name as the filename without the extension. E.g. router.ini contains only one section [router]

--- XML Config Files ---
Must only have one node inside <config> tags which is the same as the filename without the extension. E.g. router.xml is in the format <config><router>  <!-- All the properties you want here --> </router></config>

--- JSON Config Files ---
Must only contain one node which is the same as the filename without the extension. E.g. router.json is in the format {"router":{PROPERTIES HERE}}
I recommend not using JSON Config files as JSON files can't contain comments