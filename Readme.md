# TYPO3 extension "belayout_fileprovider"#


This extension makes it possible to use files for the backend layout configuration.

## Requirements ##

- TYPO3 CMS 6.2
- Some basic code for the ```ext_localconf.php``` file, see below

## How to use ##

### 1. Add the files ###

First of all you need to install the extension. Then you need to register your files. Choose one of the following options

#### By directory ####

If you define a directory, all files with the file type "ts" or "txt" within this directory are used.

```
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['BackendLayoutFileProvider']['dir'][]
    =  'EXT:belayout_fileprovider/Resources/Private/Examples/BackendLayouts/';
```

#### By extension key ####

```
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['BackendLayoutFileProvider']['ext'][]
    = $_EXTKEY;
```

Basically it is the same as "by directory" but you don't need to add the path as this is always ```EXT:<extkey>/Resources/Private/BackendLayouts/```.

#### By single file ####

You can also add a single file.

```
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['BackendLayoutFileProvider']['file'][]
    =  'EXT:belayout_fileprovider/Home.ts';
```

### 2. Adopt the TypoScript ###

To be able to use the layout, adopt your TypoScript to like the example below.
The layout file got the name ```Home.ts```, therefore the identifier for the cObj TEXT is ```file__Home```.

```
10 = FLUIDTEMPLATE
10 {
	file.stdWrap.cObject = TEXT
	file.stdWrap.cObject {
		data = levelfield:-1, backend_layout_next_level, slide
		override.field = backend_layout
		split {
			token = file__
			1.current = 1
			1.wrap = |
		}
		wrap = EXT:modernpackage/Resources/Private/Templates/|.html
	}
	layoutRootPath = EXT:modernpackage/Resources/Private/Templates/Layouts/
	variables {

	}
}
```



## Additional options ##


### Translations ###
An optional locallang.xml or locallang.xlf file in this same directory can hold a translation of the layout. The identifier in the translation file is the file name without the file extension.

### Icon ###
An optional can be defined by using the same file name and an extension of the type png, gif or jpg.
However be sure that the file is callable as there might be a rule in the .htaccess file restricting access to content on directories called Private.

To avoid any troubles, save the icon in ```Resources/Public/BackendLayouts/``` with the same name. 
