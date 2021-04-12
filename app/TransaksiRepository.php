<?php

namespace App;
use App\Transaksi;
use Validator;
use DB;

class TransaksiRepository
{
    //
    public function findById($id)
    {
        return Transaksi::with([])
        ->find($id);
    }

    public function getTransaksi()
    {
       return Transaksi::with([])->select('buku_id')->where('status','MASUK')->orwhere('status','KELUAR')->groupBy('buku_id')
            ->get();
    }

    public function getTransaksiMasuk($id)
    {
        return Transaksi::with([])->select('buku_id')->where('status','MASUK')->where('buku_id',$id)->groupBy('buku_id')->selectRaw("SUM(qty) as total")->get();
    }

    public function getTransaksiKeluar($id)
    {
        return Transaksi::with([])->select('buku_id')->where('status','KELUAR')->where('buku_id',$id)->groupBy('buku_id')->selectRaw("SUM(qty) as total")->get();
    }

    public function getTransaksiPesanan()
    {
        return Transaksi::with([])->where('status', 'PESAN')->orderby('created_at', 'ASC')->get();
    }

    public function getOutstandingOrder()
    {
        return Transaksi::with([])->where('status', 'KELUAR')->where('tgl_kembali', null)->orderby('tgl_pinjam', 'ASC')->get();
    }


    public function stok($id)
    {
        //default pesan
        $pesan = ['status' => false, 'buku_id' => $id, 'stok' => 0, 'pesan' => ''];
        $stokMasuk = $this->getTransaksiMasuk($id)->sum('total');
        $stokKeluar = $this->getTransaksiKeluar($id)->sum('total');
        $results = value($stokMasuk) - value($stokKeluar);
        if ($results > 0) {
            $pesan['status'] = true;
            $pesan['stok'] = $results;
        }
        return $pesan;
    }

    //transaksi pesan buku
    public function memberOrder($id)
    {
        $pesan = ['status' => false, 'pesan' => ''];
        try {
            $simpan = new Transaksi;
            $simpan->status = 'PESAN';
            $simpan->qty = 1;
            $simpan->buku_id = $id;
            $simpan->user_id = session()->get('user_id');
            $simpan->deskripsi = '-';
            $simpan->save();
            $pesan['status'] = true;
            $pesan['pesan'] = 'Pesanan Anda telah tersimpan, tunggu proses administrasi berikut';
            return $pesan;
        } catch (\Exception $e) {
            $pesan['status'] = false;
            $pesan['pesan'] = $e->getMessage();
            return $pesan;
        }
    }

    public function pesanan()
    {
        $results = $this->getTransaksiPesanan();
        return $results;
    }

    //transaksi pengeluaran
    public function updateTransaksiKeluar($id, $data)
    {
        $pesan = ['status' => false, 'pesan' => ''];
        $validator = Validator::make($data->post(), [
            'tgl_pinjam' => 'required',
            'catatan' => 'required',
        ]);
        if ($validator->fails()) {
            $pesan['status'] = true;
            $pesan['pesan'] = $validator->errors();
            return $pesan;
        }

        try {
            $simpan = $this->findById($id);
            $simpan->status = 'KELUAR';
            $simpan->tgl_pinjam = $data['tgl_pinjam'];
            $simpan->deskripsi = $data['catatan'];
            $simpan->update();
            $pesan['status'] = true;
            $pesan['pesan'] = 'Order telah berhasil diproses';
            return $pesan;
        } catch (\Exception $e) {
            $pesan['status'] = false;
            $pesan['pesan'] = $e->getMessage();
            return $pesan;
        }
    }


    //transaksi penerimaan
    public function updateTransaksiMasuk($id, $data)
    {
        $pesan = ['status' => false, 'pesan' => ''];
        $validator = Validator::make($data->post(), [
            'tgl_kembali' => 'required',
        ]);
        if ($validator->fails()) {
            $pesan['status'] = true;
            $pesan['pesan'] = $validator->errors();
            return $pesan;
        }
        
        try {
            $simpan = $this->findById($id);
            $simpan->status = 'SELESAI';
            $simpan->tgl_kembali = $data['tgl_kembali'];
            $simpan->update();
            $pesan['status'] = true;
            $pesan['pesan'] = 'Order telah berhasil diproses';
            return $pesan;
        } catch (\Exception $e) {
            $pesan['status'] = false;
            $pesan['pesan'] = $e->getMessage();
            return $pesan;
        }
    }


    public function penerimaan()
    {
        $results = $this->getOutstandingOrder();
        return $results;
    }

    //transaksi penyesuaian
    public function transaksiPenyesuaian($data)
    {
        $pesan = ['status' => false, 'pesan' => ''];
        $validator = Validator::make($data->post(), [
            'field_judul' => 'required',
            'field_pengarang' => 'required',
            'field_kategori' => 'required',
            'field_rak' => 'required',
            'field_date' => 'required',
            'field_qty' => 'required',
        ]);
        if ($validator->fails()) {
            $pesan['status'] = true;
            $pesan['pesan'] = $validator->errors();
            return $pesan;
        }
        
        try {
            DB::beginTransaction();
            //buat master buku baru
            $simpanBuku = new Buku;
            $simpanBuku->nama = $data['field_judul'];
            $simpanBuku->deskripsi = $data['field_pengarang'];
            $simpanBuku->kategori = $data['field_kategori'];
            $simpanBuku->rak_id = $data['field_rak'];
            $simpanBuku->save();

            $simpan = new Transaksi;
            $simpan->status = 'MASUK';
            $simpan->qty = $data['field_qty'];
            $simpan->buku_id = $simpanBuku->id;
            $simpan->tgl_kembali = $data['field_date'];
            $simpan->user_id = session()->get('user_id');
            $simpan->deskripsi = $data['field_deskripsi'];
            $simpan->save();
            DB::commit();
            $pesan['status'] = true;
            $pesan['pesan'] = 'Data telah tersimpan';
            return $pesan;
        } catch (\Exception $e) {
            DB::rollback();
            $pesan['status'] = false;
            $pesan['pesan'] = $e->getMessage();
            return $pesan;
        }
    }


    
}
