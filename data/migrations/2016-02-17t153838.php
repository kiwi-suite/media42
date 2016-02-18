<?php
class Migration20160217153838
{

    public function up(Zend\ServiceManager\ServiceManager $serviceManager)
    {
        $sql = "CREATE TABLE `media42_media` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `directory` varchar(255) NOT NULL,
          `filename` varchar(255) NOT NULL,
          `category` VARCHAR(255) NOT NULL DEFAULT 'default',
          `title` varchar(255) DEFAULT NULL,
          `description` varchar(255) DEFAULT NULL,
          `keywords` varchar(1000) DEFAULT NULL,
          `mimeType` varchar(255) NOT NULL,
          `size` int(10) unsigned NOT NULL,
          `meta` longtext,
          `updated` datetime NOT NULL,
          `created` datetime NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
        $serviceManager->get('Db\Master')->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
    }

    public function down(Zend\ServiceManager\ServiceManager $serviceManager)
    {
        $sql = "DROP TABLE `media42_media`";
        $serviceManager->get('Db\Master')->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
    }
}
