<?php

namespace Mhajdu\Assignable;

trait Assignable {
    public function assign($model) {
        if($this->assignable_class === null || class_exists($this->assignable_class) === false) {
            throw new \Exception('You must define the $assignable_class property in your model.');
        }

        $assignable_class = new $this->assignable_class;
        $assignable_table = $assignable_class->getTable();

        $columns = $this->getConnection()->getSchemaBuilder()->getColumnListing($assignable_table);

        //search columns for parent and child, if column name has parent keyword then it is parent column, throw new exception if not found
        $parent_column = null;
        $child_column = null;
        foreach($columns as $column) {
            if(strpos($column, 'parent') !== false) {
                $parent_column = $column;
            }
            if(strpos($column, 'child') !== false) {
                $child_column = $column;
            }
        }

        if($parent_column === null || $child_column === null) {
            throw new \Exception('Could not find parent or child columns.');
        }

        $assignable_class->create([
            $parent_column => $this->getKey(),
            $child_column => $model->getKey()
        ]);

        return true;
    }
}