<?php

/**
 * This file is part of the OpenPNE package.
 * (c) OpenPNE Project (http://www.openpne.jp/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

/**
 * opAPICommunity
 *
 * @package    OpenPNE
 * @subpackage api
 * @author     Kousuke Ebihara <ebihara@tejimaya.com>
 */
class opAPICommunity extends opAPI implements opAPIInterface
{
  public function feed()
  {
    $this
      ->addConditionPublished()
      ->addConditionUpdated()
      ->setOrderBy()
      ->setOffsetAndLimitation();

    $communities = $this->getRouteObject()->execute();
    if (!$communities->count())
    {
      return false;
    }

    $feed = $this->getGeneralFeed('Communities', $this->getTotalCount());
    foreach ($communities as $key => $community)
    {
      $entry = $feed->addEntry();
      $this->createEntryByInstance($community, $entry);
    }
    $feed->setUpdated($communities->getFirst()->getCreatedAt());

    return $feed->publish();
  }

  public function entry()
  {
    return $this->getRouteObject()->fetchOne();
  }

  public function insert(SimpleXMLElement $xml)
  {
    return false;
  }

  public function update(SimpleXMLElement $xml)
  {
    return false;
  }

  public function delete()
  {
    return false;
  }

  public function generateEntryId($entry)
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('Url', 'opUtil'));

    return app_url_for('pc_frontend', 'community/home?id='.$entry->getId(), true);
  }

  public function createEntryByInstance(Doctrine_Record $community, SimpleXMLElement $entry = null)
  {
    $entry = parent::createEntryByInstance($community, $entry);
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('Url', 'opUtil'));
    $entry->setTitle($community->getName());
    $entry->setContent($community->getConfig('description'));

    $entry->setLink(url_for('@feeds_community_retrieve_resource_normal?model=community&id='.$community->getId()), 'self', 'application/atom+xml');
    $entry->setLink(app_url_for('pc_frontend', 'community/home?id='.$community->getId(), true), 'alternate', 'text/html');
    $entry->setLink(app_url_for('mobile_frontend', 'community/home?id='.$community->getId(), true), 'alternate');

    return $entry;
  }
}