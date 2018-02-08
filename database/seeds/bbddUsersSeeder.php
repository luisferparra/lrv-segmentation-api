<?php


use Illuminate\Database\Seeder;



/**
 * Seeder para pasar datos de crm-api-segmentation.bbdd_subscribers a bbdd_users (de antigua funcionalidad a nueva)
 */

class bbddUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        ini_set('memory_limit', '4G');
        set_time_limit(0);


        $bbddArr = [];
        //Lo primero será coger las bbdds
        $bbddTmp = DB::connection('segmentation')->table('bbdd_lists')->get();
        foreach ($bbddTmp as $key => $bbddItem) {
            $bbddArr[$bbddItem->val] = $bbddItem->id;
        }
        $db = DB::connection('segmentation')->getPdo();
        $q = "SELECT id,bbdd_subscribed as val FROM bbdd_subscribers";
        $query = $db->prepare($q);
        $query->execute();
        $arrIns = [];
        $cont = 0;
        while ($item = $query->fetch()) {
			// process data
            //dd($item);
            $val = explode(',', $item['val']);
            foreach ($val as $userBbdd) {
               $arrIns [] = ['id'=>$item['id'],'id_val'=>$bbddArr[strtoupper($userBbdd)]];
            }
            $cont ++;
            if ($cont>2000) {
                $cont = 0;
                DB::connection('segmentation')->table('bbdd_users')->insertIgnore($arrIns);
                unset($arrIns);
                $arrIns = [];

            }
        }
        if (!empty($arrIns))
        DB::connection('segmentation')->table('bbdd_users')->insertIgnore($arrIns);
       

    }
}
