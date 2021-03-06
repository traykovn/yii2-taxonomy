Yii2 Taxonomy
=============
[![Build Status](https://travis-ci.org/nkostadinov/yii2-taxonomy.svg?branch=master)](https://travis-ci.org/nkostadinov/yii2-taxonomy)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/nkostadinov/yii2-taxonomy/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/nkostadinov/yii2-taxonomy/?branch=master)

Yii2 Taxonomy management. A component which adds generic taxonomy functionalities to your application. The component
comes with a couple of term definitions(tags, properties). These additional info is added via addition tables created
by the extension. The extension also offers a search behavior which can be attached to AR instances for easier searching.

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist nkostadinov/yii2-taxonomy "*"
```

or add

```
"nkostadinov/yii2-taxonomy": "*"
```

to the require section of your `composer.json` file.

Then you need to configure the taxonomy component to your configuration file.

```
    'components' => [
        .......
        'taxonomy' => [
            'class' => 'nkostadinov\taxonomy\Taxonomy',
        ],
        .......
```        

Finally you need to run the initial migration
```
yii migrate --migrationPath=@nkostadinov/taxonomy/migrations
```

Usage
-----

If you need to use the management interface for taxonomies you must add the Taxonomy module to you configuration

```    
    'modules' => [
        ......
        'taxonomy' => [
            'class' => 'nkostadinov\taxonomy\Module'
        ],
```        

It is recommended to use the taxonomy *MODULE* only on dev environment to create the taxonomies just like gii. When you add a taxonomy via the interface the component creates a migration so you can execute it on production later.
 
Sample usage (tags):
 //return a taxonomy object used to manipulate this taxonomy(taxonomy must be defined before that and the migrations executed)
 ```
 $taxonomy = Yii::$app->taxonomy->getTerm('post_tags');
 ```
 
 //Adding terms (e.g. tags) to an object with id $post_id
 ```
 $taxonomy->addTerm($post_id, ['read', 'important', 'new']);
 ```
 
 //Deleting tags
 ```
 $taxonomy->removeTerm($post_id, ['important', 'new']);
 ```
 
 //Retrieving tags
 ```
 $taxonomy->getTerms($this->id);
 ```
 // returns ['read']
 
## Taxonomies
The bundled taxonomies with these package are :

### TagTerm
Basically tag represent taxonomies which are added to an object. They do not have a value. You can add multiple tags to an item.

### PropertyTerm
They are the same as tag terms but they DO have a value. You can add multiple properties to an item.

### CategoryTerm
Ability to create hierarchical terms (e.g. categories)
