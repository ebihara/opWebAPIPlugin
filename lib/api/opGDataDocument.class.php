<?php

/**
 * This file is part of the OpenPNE package.
 * (c) OpenPNE Project (http://www.openpne.jp/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

/**
 * opGDataDocument
 *
 * @package    opWebAPIPlugin
 * @subpackage api
 * @author     Kousuke Ebihara <ebihara@tejimaya.com>
 */
abstract class opGDataDocument
{
  protected $elements;

  const XML_DECLARATION = '<?xml version="1.0" encoding="UTF-8"?>';
  const NAMESPACE = 'http://schemas.google.com/g/2005';

  public function __construct($input = '')
  {
    if ($input)
    {
      $xml = @simplexml_load_string($input);
      if (!$xml)
      {
        throw new RuntimeException('The inputed data is not a valid XML.');
      }
      $this->elements = $xml;
    }
    else
    {
      $this->elements = simplexml_load_string($this->getRootXMLString());
    }
  }

  public function publish()
  {
    $elements = $this->getElements();
    $result = $elements->asXML();

    if (Doctrine::getTable('SnsConfig')->get('op_web_api_plugin_using_cdata', false))
    {
      $result = preg_replace('/<content type="(.+?)">(.+?)<\/content>/ims', '<content type="$1"><![CDATA[$2]]></content>', $result, -1, $count);
      $result = preg_replace('/<title type="(.+?)">(.+?)<\/title>/ims', '<title type="$1"><![CDATA[$2]]></title>', $result, -1, $count);
    }

    return $result;
  }

  abstract protected function getRootXMLString();

  public function getElements()
  {
    return $this->elements;
  }
}
