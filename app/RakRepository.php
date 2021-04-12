<?php

namespace App;
use App\Rak;
use Validator;

class RakRepository
{
    //
    public function findById($id)
    {
        return Rak::with([])
        ->find($id);
    }

    public function getRak()
    {
       return Rak::with(['bukuRak'])
            ->get();
    }

    //Fungsi Simpan dan Update
    public function createAndUpdateRak($id = null, $data)
    {

        $pesan = ['status' => false, 'pesan' => ''];

        $validator = Validator::make($data->post(), [
            'field_deskripsi_rak' => 'required',
        ]);
        if ($validator->fails()) {
            $pesan['status'] = true;
            $pesan['pesan'] = $validator->errors();
            return $pesan;
        }
        
        try {
        $simpan = $this->findById($id);
        if(!$simpan){
            $simpan = new Rak();
        }
        $simpan->deskripsi  = $data['field_deskripsi_rak'];
        $simpan->save();
        $pesan['status'] = true;
        $pesan['pesan'] = 'Data telah tersimpan';
        return $pesan;
        } catch (\Exception $e) {
            $pesan['status'] = false;
            $pesan['pesan'] = $e->getMessage();
            return $pesan;
        }
    } 
}
