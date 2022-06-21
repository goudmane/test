## Usage
@chunks (Default = 10)

use chunks to avoid time execution exceeded

@mode (Default = 'sql')

sql mode can generate Update if find bien in database esle will generate insert statment;
```php

# show data simplify mode 
$url = 'http://localhost/tesr/vilanovo.php?mode=simplify&chunks=10';

# show data progress mode 
$url = 'http://localhost/tesr/vilanovo.php?mode=progress&chunks=10';

# Data tp sql mode
$url = 'http://localhost/tesr/vilanovo.php?mode=sql&chunks=10';