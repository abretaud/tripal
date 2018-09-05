<?php

namespace Tests;

use PHPUnit\Exception;
use StatonLab\TripalTestSuite\DBTransaction;
use StatonLab\TripalTestSuite\TripalTestCase;

module_load_include('inc', 'tripal_chado', 'includes/api/ChadoRecord');


class ChadoRecordTest extends TripalTestCase {

  use DBTransaction;


  /**
   * Data provider.  A variety of chado records.
   *
   * @return array
   */
  public function recordProvider() {
    //table, factory or NULL, record_id or NULL
    $a = factory('chado.feature')->create();
    $b = factory('chado.organism')->create();

    return [
      ['feature', NULL, NULL],
      ['feature', $a->feature_id, $a],
      ['organism', $b->organism_id, $b],
    ];
  }

  /**
   * Tests that the class can be initiated with or without a record specified
   *
   * @group api
   * @group chado
   * @group wip
   * @dataProvider recordProvider
   */
  public function testInitClass($table, $id, $factory) {
    $record = new \ChadoRecord($table, $id);
    $this->assertNotNull($record);
  }

  /**
   * @group api
   * @group chado
   * @group wip
   * @throws \Exception
   * @dataProvider recordProvider
   */

  public function testGetTable($table, $id, $factory) {
    $record = new \ChadoRecord($table, $id);
    $this->assertEquals($table, $record->getTable());
  }

  /**
   * @group wip
   * @group api
   * @group chado
   * @dataProvider recordProvider
   *
   * @throws \Exception
   */
  public function testGetID($table, $id, $factory) {
    $record = new \ChadoRecord($table, $id);
    $returned_id = $record->getID();
    if ($id) {
      $this->assertEquals($id, $returned_id);
    }
    else {
      $this->assertNull($returned_id);
    }
  }

  /**
   * @group api
   * @group wip
   * @group chado
   * @dataProvider recordProvider
   *
   *
   */
  public function testGetValues($table, $id, $factory) {
    $record = new \ChadoRecord($table, $id);

    if (!$id) {
      $returned_vals = $record->getValues();
      $this->assertEmpty($returned_vals);
    }
    else {
      $values = $record->getValues();
      $this->assertNotEmpty($values);
      foreach ($factory as $key => $value) {
        $this->assertArrayHasKey($key, $values);
        $this->assertEquals($value, $values[$key]);
      }

    }
  }

  /**
   * @group api
   * @group wip
   * @group chado
   * @dataProvider recordProvider
   *
   */
  public function testGetValue($table, $id, $factory) {
    $record = new \ChadoRecord($table, $id);

    if (!$id) {
      $returned_id = $record->getValue($table . '_id');
      $this->assertNull($returned_id);
    }
    else {
      foreach ($factory as $key => $value) {
        $returned_value = $record->getValue($key);
        $this->assertEquals($value, $returned_value);
      }
    }
  }

  /**
   * @group wip
   * @group chado
   * @group api
   * @dataProvider recordProvider
   */

  public function testFind($table, $id, $factory) {

    $record = new \ChadoRecord($table);

    $values = (array) $factory;
    $record->setValues($values);
    if ($id) {
      $found = $record->find();

      $this->assertNotNull($found);
      $this->assertEquals(1, $found);
    }
    else {
      //There isnt a record in the DB, so find should throw an exception
      $record->setValue($table . '_id', 'unfindable');
      $this->expectException(Exception);
      $found = $record->find();
    }
  }

  /**
   * This test will not use providers.
   */
  public function testSetValue() {

    $record = new \ChadoRecord('feature');
    $values = [];
    $record->setValues($values);

  }

}
