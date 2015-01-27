# Module: Twitter

## Installation

Visit the [Fork CMS knowledge base](http://fork-cms.com/knowledge-base) to learn how to install a module. To download the zip-package go to the [extension page](http://www.fork-cms.com/extensions/detail/twitter) of the module on fork-cms.com.

## Updating

Update from 2.0.0 to 2.1.0? Just apply this SQL update to your database and your done.

```
ALTER TABLE `twitter_users` CHANGE `twitter_id` `twitter_id` BIGINT(20) UNSIGNED NULL DEFAULT NULL;
```

## Contributing

This module stays up-to-date by community efforts. It would be a pleasure to have you as part of it. GitHub does a great job in managing this collaboration by providing different tools, the only thing you need is a [GitHub](https://github.com/) login.

* Use **Pull requests** to add or update code
* **Issues** for bug reporting or code discussions
* Or regarding documentation and how-to's, check out **Wiki**

More info on how to work with GitHub on [help.github.com](https://help.github.com).

## License

All modules, including this one, which are maintained by the Fork CMS community have the same license as the CMS itself which is called MIT. In short, this license allows you to do everything as long as the copyright statement stays present.
