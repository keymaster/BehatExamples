<?php

namespace Doctrine\ODM\MongoDB\Tests\Functional\Ticket;

use Doctrine\ODM\MongoDB\Events;

class MODM83Test extends \Doctrine\ODM\MongoDB\Tests\BaseTest
{
    private function getDocumentManager()
    {
        $this->listener = new MODM83EventListener();
        $evm = $this->dm->getEventManager();
        $events = array(
            Events::preUpdate,
            Events::postUpdate,
        );
        $evm->addEventListener($events, $this->listener);
        return $this->dm;
    }

    public function testDocumentWithEmbeddedDocumentNotUpdated()
    {
        $dm = $this->getDocumentManager();

        $won = new MODM83TestDocument();
        $won->name = 'Parent';
        $won->embedded = new MODM83TestEmbeddedDocument();
        $won->embedded->name = 'Child';
        $too = new MODM83OtherDocument();
        $too->name = 'Neighbor';
        $dm->persist($won);
        $dm->persist($too);
        $dm->flush();
        $dm->clear();

        $won = $dm->find(__NAMESPACE__.'\MODM83TestDocument', $won->id);
        $too = $dm->find(__NAMESPACE__.'\MODM83OtherDocument', $too->id);
        $too->name = 'Bob';
        $dm->flush();
        $dm->clear();

        $called = array(
            Events::preUpdate  => array(__NAMESPACE__.'\MODM83OtherDocument'),
            Events::postUpdate => array(__NAMESPACE__.'\MODM83OtherDocument')
        );
        $this->assertEquals($called, $this->listener->called);
    }
}

class MODM83EventListener
{
    public $called = array();
    public function __call($method, $args)
    {
        $document = $args[0]->getDocument();
        $className = get_class($document);
        $this->called[$method][] = $className;
    }
}

/** @Document */
class MODM83TestDocument
{
    /** @Id */
    public $id;

    /** @String */
    public $name;

    /** @EmbedOne(targetDocument="MODM83TestEmbeddedDocument") */
    public $embedded;
}

/** @EmbeddedDocument */
class MODM83TestEmbeddedDocument
{
    /** @String */
    public $name;
}

/** @Document */
class MODM83OtherDocument
{
    /** @Id */
    public $id;

    /** @String */
    public $name;
}