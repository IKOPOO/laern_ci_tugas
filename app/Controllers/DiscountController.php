<?php

namespace App\Controllers;

use App\Models\DiscountModel;

class DiscountController extends BaseController
{
  protected $discount;

  function __construct()
  {
    $this->discount = new DiscountModel();
  }

  public function index()
  {
    $discounts = $this->discount->findAll();
    $data['discount'] = $discounts;
    return view('v_discount', $data);
  }

  public function create()
  {
    $tanggal = $this->request->getPost('tanggal');
    $nominal = $this->request->getPost('nominal');

    $check = $this->discount->where('tanggal', $tanggal)->first();
    if ($check) {
      return redirect('discount')->with('failed', 'Tanggal tidak boleh sama');
    }

    $dataForm = [
      'tanggal'    => $this->request->getPost('tanggal'),
      'nominal'    => $this->request->getPost('nominal'),
      'created_at' => date("Y-m-d H:i:s")
    ];

    $this->discount->insert($dataForm);
    
    if ($tanggal == date('Y-m-d')) {
        session()->set('diskon', $nominal);
    }

    return redirect('discount')->with('success', 'Diskon berhasil ditambahkan');
  }

  public function edit($id)
  {
    $tanggal = $this->request->getPost('tanggal');
    $nominal = $this->request->getPost('nominal');

    $check = $this->discount->where('tanggal', $tanggal)->where('id !=', $id)->first();
    if ($check) {
      return redirect('discount')->with('error', 'Tanggal Sudah dipakai, pilih tanggal lain');
    }

    $dataForm = [
      'tanggal'    => $tanggal,
      'nominal'    => $nominal,
      'updated_at' => date("Y-m-d H:i:s")
    ];

    $this->discount->update($id, $dataForm);
    return redirect('discount')->with('success', 'Diskon berhasil diubah');
  }

  public function delete($id)
  {
    $diskon = $this->discount->find($id);
    if ($diskon) {
      $this->discount->delete($id);

      if($diskon['tanggal'] == date('Y-m-d')) {
        session()->remove('diskon'); 
      }
      return redirect('discount')->with('success', 'Diskon berhasil dihapus');
    }
    return redirect('discount')->with('failed', 'Diskon tidak ditemukan');
  }
}
