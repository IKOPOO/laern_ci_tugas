<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use App\Models\TransactionModel;
use App\Models\TransactionDetailModel;

class TransaksiController extends BaseController{

  protected $cart;
  protected $client;
  protected $apikey;
  protected $transaction;
  protected $transaction_detail;

  function __construct(){
    helper('number');
    helper('form');
    $this->cart = \Config\Services::cart();
    $this->client = new \GuzzleHttp\Client();
    $this->apikey = env('COST_KEY');
    $this->transaction = new TransactionModel();
    $this->transaction_detail = new TransactionDetailModel();
  }

  public function index(){
    $data['items'] = $this->cart->contents();
    $data['total'] = $this->cart->total();
    return view('v_keranjang', $data);
  }


  public function cart_add(){
    log_message('debug', 'Session di cart_add: ' . print_r(session()->get(), true));
    // dd(session()->get());


    $id = $this->request->getPost('id');
    $nama = $this->request->getPost('nama');
    $harga = floatval($this->request->getPost('harga'));
    $foto = $this->request->getPost('foto');
    $qty = 1;

    $diskon = floatval(session()->get('diskon'));

    $hargaDiskon = max(0, $harga - $diskon);
    $this->cart->insert([
     'id'        => $id,
     'qty'       => $qty,
     'price'     => $hargaDiskon,
     'name'      => $nama,
     'options'   => [
      'foto' => $foto,
      'hargaAsli' => $harga,
      'diskon' => $diskon
     ]
    ]);
    session()->setflashdata(
      'success',
      'Produk berhasil ditambahkan ke keranjang. (<a href="' . base_url() .
      'keranjang">Lihat</a>)'
    );
    return redirect()->to(base_url('/'));
  }

  public function cart_clear(){
    $this->cart->destroy();
    session()->setflashdata('success', 'Keranjang Berhasil Dikosongkan');
    return redirect()->to(base_url('keranjang'));
  }

  public function cart_edit(){
    $i = 1;
    foreach ($this->cart->contents() as $value) {
      $this->cart->update(array(
        'rowid' => $value['rowid'],
        'qty'   => $this->request->getPost('qty' . $i++)
        ));
    }

    session()->setflashdata('success', 'Keranjang Berhasil Diedit');
    return redirect()->to(base_url('keranjang'));
  }

  public function cart_delete($rowid){
    $this->cart->remove($rowid);
    session()->setflashdata('success', 'Keranjang Berhasil Dihapus');
    return redirect()->to(base_url('keranjang'));
  }

  public function checkout(){
    $data['items'] = $this->cart->contents();
    $data['total'] = $this->cart->total();

    return view('v_checkout', $data);
  }


  public function getLocation(){
		//keyword pencarian yang dikirimkan dari halaman checkout
    $search = $this->request->getGet('search');

    $response = $this->client->request(
      'GET',
      'https://rajaongkir.komerce.id/api/v1/destination/domestic-destination?search='.$search.'&limit=50',[
        'headers' => [
          'accept' => 'application/json',
          'key' => $this->apikey,
        ],
      ]
    );

    $body = json_decode($response->getBody(), true);
    return $this->response->setJSON($body['data']);
  }

  public function getCost(){
		//ID lokasi yang dikirimkan dari halaman checkout
    $destination = $this->request->getGet('destination');

		//parameter daerah asal pengiriman, berat produk, dan kurir dibuat statis
    //valuenya => 64999 : PEDURUNGAN TENGAH , 1000 gram, dan JNE
    $response = $this->client->request(
      'POST',
      'https://rajaongkir.komerce.id/api/v1/calculate/domestic-cost', [
        'multipart' => [
          [
            'name' => 'origin',
            'contents' => '64999'
          ],
          [
            'name' => 'destination',
            'contents' => $destination
          ],
          [
            'name' => 'weight',
            'contents' => '1000'
          ],
          [
            'name' => 'courier',
            'contents' => 'jne'
          ]
        ],
        'headers' => [
          'accept' => 'application/json',
          'key' => $this->apikey,
        ],
      ]
    );

    $body = json_decode($response->getBody(), true);
    return $this->response->setJSON($body['data']);
  }


  public function buy(){
    if ($this->request->getPost()) {
      $dataForm = [
        'username' => $this->request->getPost('username'),
        'total_harga' => $this->request->getPost('total_harga'),
        'alamat' => $this->request->getPost('alamat'),
        'ongkir' => $this->request->getPost('ongkir'),
        'status' => 0,
        'created_at' => date("Y-m-d H:i:s"),
        'updated_at' => date("Y-m-d H:i:s")
      ];

      $this->transaction->insert($dataForm);

      $last_insert_id = $this->transaction->getInsertID();
      $diskon = floatval(session()->get('diskon'));

      foreach ($this->cart->contents() as $value) {
        $hargaAsli   = floatval($value['options']['hargaAsli']);
        $hargaDiskon = max(0, $hargaAsli - $diskon);

        $dataFormDetail = [
          'transaction_id' => $last_insert_id,
          'product_id' => $value['id'],
          'jumlah' => $value['qty'],
          'harga_asli' => $hargaAsli,
          'harga_diskon' => $hargaDiskon,
          'diskon' => $diskon,
          'subtotal_harga' => $hargaDiskon * $value['qty'],
          'created_at' => date("Y-m-d H:i:s"),
          'updated_at' => date("Y-m-d H:i:s")
        ];

        $this->transaction_detail->insert($dataFormDetail);
      }

      $this->cart->destroy();
      return redirect()->to(base_url());
    }
  }
}
