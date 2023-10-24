<?php

namespace WPDataAccess\Data_Tables;

// Add Search Panes support.
use  WPDataAccess\WPDA ;
class WPDA_Search_Panes
{
    private  $wpdadb ;
    private  $table_name ;
    private  $columns ;
    private  $default_where ;
    private  $is_cascading ;
    private  $draw ;
    private  $where = "" ;
    private  $search_panes = array() ;
    public function __construct(
        $wpdadb,
        $table_name,
        $columns,
        $default_where,
        $draw
    )
    {
    }
    
    public function get_where()
    {
        return $this->where;
    }
    
    public function get_panes()
    {
        return $this->search_panes;
    }
    
    public function sp()
    {
    }
    
    public function sp_panes( $columns, $where = '' )
    {
        $sp = array();
        return $sp;
    }
    
    public function sp_where( $search_panes )
    {
        $where = '';
        return $where;
    }

}