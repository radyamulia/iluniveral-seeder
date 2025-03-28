<nav class="bg-white navbar navbar-expand-lg fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="https://bph.univeral.ac.id/">
            <img src="image/bph-univeral.png" alt="Bootstrap" width="230" height="50">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="mb-2 navbar-nav me-auto mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="/mahasiswa">Jumlah Mahasiswa</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="#">Grafik PDDIKTI</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="/ipepa">IPEPA</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="#">Mahasiswa</a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link active" href="#">AKM</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link active dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        MBKM
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">MBKM Prodi</a></li>
                        <li><a class="dropdown-item" href="#">MBKM Fakultas</a></li>
                    </ul>
                </li>
                <li class="nav-item ">
                    <a class="nav-link active" target="_blank" href="https://univeral.ac.id/">Profil Universitas</a>
                </li>
            </ul>
            <div class="d-flex">
                <a href="/login">
                    <button type="button" class="btn btn-primer">Login</button>
                </a>
            </div>
        </div>
    </div>
</nav>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="POST" action="/login">
            <div class="text-black modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalTitle">Login Admin</h5>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" id="password" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"
                        id="closeModalBtn">Close</button>
                    <button type="submit" class="btn btn-primer">Login</button>
                </div>
            </div>
        </form>
    </div>
</div>

<nav class="navbar navbar-expand-lg bg-primer">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">
            <img src="image/bph-univeral.png" alt="Bootstrap" width="230" height="50">
        </a>
    </div>
</nav>
