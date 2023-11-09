<?php

namespace App\Http\Controllers;

use App\User;
use App\TableColumn;
use App\CategoryTable;
use App\UserSellProduc;
use App\UserProfessional;
use App\UserAdvertisment;
use App\ProfessionalTableData;

use Illuminate\Http\Request;

class ReportsController extends Controller
{
    /**
     * Display a report list.
     *
     * @return \Illuminate\Http\Response
     */
    public function professionalUser($id)
    {
		$tableReport = ProfessionalTableData::where('category_table_id', $id)->groupBy(['user_id'])->get();
        $tableData  = CategoryTable::where('id', $id)->first('table_name');
        $tableName = $tableData->table_name;
        return view('reports.professional-user', ['tableReport' => $tableReport, 'tableName' => $tableName]);
    }

    /**
     * @Function: Professional List
     */
    public function professionalList($uid, $id){
        $professionalData = ProfessionalTableData::where(['category_table_id' => $id, 'user_id' => $uid])->groupBy('row_number')->get();
        $professionalTable = ProfessionalTableData::where(['category_table_id' => $id, 'user_id' => $uid])->groupBy('table_number')->get();
        $professionalResult = ProfessionalTableData::where(['category_table_id' => $id, 'user_id' => $uid])->orderBy('id', 'asc')->get();
        $tableData  = CategoryTable::where('id', $id)->first('table_name');
        $tableName = $tableData->table_name;
        $tableColumnData = TableColumn::where('category_table_id', $id)->get();
        return view('reports.professional-list', ['professionalData' => $professionalData, 'tableName' => $tableName, 'professionalResult' => $professionalResult, 'tableColumnData' => $tableColumnData, 'professionalTable' => $professionalTable]);
    }

    /**
     *@Function: Change Table Row Status 
     */
    public function changeTableRowStatus($rowid, $status){
        $rowaffected = ProfessionalTableData::where('row_number', $rowid)->update(['status' => $status]);        
        return $rowaffected;
    }
}
