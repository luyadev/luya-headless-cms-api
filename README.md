<p align="center">
  <img src="https://raw.githubusercontent.com/luyadev/luya/master/docs/logo/luya-logo-0.2x.png" alt="LUYA Logo"/>
</p>

# LUYA HEADLESS CMS API BRIDGE

[![LUYA](https://img.shields.io/badge/Powered%20by-LUYA-brightgreen.svg)](https://luya.io)

This module provides and out of the box ready **API** in order to consume the CMS informations.

+ The API is public and therefore requires not Authentication.
+ CORS works out of the box.
+ Only needed informations will be exposed
+ Its optimized with caching, which makes it very fast! (@TBD ;-))

This can turn your exists LUYA website into an API which you can used to build your website with a JavaScript Framework like VUE or Angular!

## Installation

Install the extension through composer:

```sh
composer require luyadev/luya-headless-cms-api
```

Add the module to the config

```php
'modules' => [
    'api' => [
        'class' => 'luya\headless\cms\api\Module',
    ]
]
```

## APIs 

> The  module name is equal to the rest api prefix. When you register the module as `foobar` in the config the api would be `/foobar/menu?langId=x`.

#### Menu

|API|Example|Description
|---|-------|-----------
|`api/menu/containers`||Returns all available CMS Menu Containers. LUYA will create a `content` default container while setup.
|`api/menu?langId=`|`{
  "default": {
    "id": 1,
    "name": "Menu",
    "alias": "default",
    "items": [
      {
        "id": 1,
        "index": 1,
        "nav_id": 1,
        "lang_id": 1,
        "is_hidden": true,
        "is_home": true,
        "title": "Startseite",
        "title_tag": null,
        "alias": "startseite",
        "path": "startseite",
        "description": null,
        "children": [],
        "has_children": false
      },
      {
        "id": 2,
        "index": 2,
        "nav_id": 2,
        "lang_id": 1,
        "is_hidden": false,
        "is_home": false,
        "title": "Lets Talk",
        "title_tag": "",
        "alias": "letstalk",
        "path": "letstalk",
        "description": "Genuss und Freude.",
        "children": [],
        "has_children": false
      },
      {
        "id": 3,
        "index": 4,
        "nav_id": 3,
        "lang_id": 1,
        "is_hidden": false,
        "is_home": false,
        "title": "About Me",
        "title_tag": null,
        "alias": "aboutme",
        "path": "aboutme",
        "description": null,
        "children": [],
        "has_children": false
      }
    ]
  },
  "footer": {
    "id": 2,
    "name": "Footer",
    "alias": "footer",
    "items": [
      {
        "id": 6,
        "index": 1,
        "nav_id": 6,
        "lang_id": 1,
        "is_hidden": false,
        "is_home": false,
        "title": "Datenschutz",
        "title_tag": null,
        "alias": "datenschutz",
        "path": "datenschutz",
        "description": null,
        "children": [],
        "has_children": false
      },
      {
        "id": 7,
        "index": 2,
        "nav_id": 7,
        "lang_id": 1,
        "is_hidden": false,
        "is_home": false,
        "title": "Impressum",
        "title_tag": null,
        "alias": "impressum",
        "path": "impressum",
        "description": null,
        "children": [],
        "has_children": false
      }
    ]
  }
}`|Returns the page tree (menu) for a given language `api/menu?langId=1`. In order to return only visible items add `&onlyVisible=1`

#### Content 

|API|Example|Description
|---|-------|-----------
|`api/page?id=`||Returns the placeholders with all blocks for a certain page: `api/page?id=8`
|`api/page/nav?id=&langId=`||Returns the placeholders with all blocks for a certain nav id with the corresponding language id
|`api/page/home?langId=`||Returns the content of the homepage for the given language

> All APIs expect a GET request.

## VUE

> Proof of concept example in VUE

Create a component for the given Element, in this case we are using the Html Block `LuyaCmsFrontendBlocksHtmlBlock.vue`:

```vue
<template>
  <div v-html="block.values.html" />
</template>

<script>
export default {
  props: {
    block: Object
  }
}
</script>
```

Create a component which loads the page and foreaches the components:

```vue
<template>
  <div v-if="isLoaded">
      <h1>{{ this.title }}</h1>
      <component 
        v-for="item in contentPlaceholder" 
        :key="item.id" 
        :is="item.block_name" 
        :block="item"
      />
  </div>
</template>

<script>
import LuyaCmsFrontendBlocksHtmlBlock from '../../components/LuyaCmsFrontendBlocksHtmlBlock.vue'

export default {
  components: { LuyaCmsFrontendBlocksHtmlBlock },
  data: () => ({
    isLoaded: false,
    response: null
  }),
  computed: {
    contentPlaceholder () {
      return this.isLoaded ? this.response.placeholders.content : []
    }
  },
  async mounted () {
    const { data } = await this.$axios('page?id=' + this.$route.params.slug)
    this.response = data
    this.isLoaded = true
  }
}
</script>
```
