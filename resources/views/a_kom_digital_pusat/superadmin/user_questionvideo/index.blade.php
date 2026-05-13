@extends('layouts.KomDigital')
@section('content')
 <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Jawaban User atas Pertanyaan</h4>
                    <div class="card-tools">
                        <a href="" class="btn btn-primary btn-sm">+ Tambah
                            Jawaban</a>
                    </div>
                </div>

                <div class="card-body">
                  

                    <div class="table-responsive b-0" data-pattern="priority-columns">
                        <table id="tech-companies-1" class="table table-striped text-truncate">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>User</th>
                                    <th>Pertanyaan</th>
                                    <th>Jawaban</th>
                                    <th>Benar?</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                               
                            </tbody>
                        </table>
                    </div>
                     <div class="pagination justify-content-center mt-4">
                        {{-- {{ $jawabans->links('pagination::bootstrap-4') }} --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection