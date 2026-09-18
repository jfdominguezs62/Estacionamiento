<?php 
include ( '../constants.php' );
Constants::setpath_root  ("../");
Constants::create_filejs( true );

include ( Constants::getpath_root() . 'config.php' );
include ( Constants::getpath_tweb() . 'core.php' );
include ( Constants::getpath_root() . 'helpers.php' );

include('view_header.php');
?>

<h4 class="fw-bold mb-3"><i class="fas fa-tags me-2"></i>Tarifas</h4>

<div class="card border-0 shadow-sm mb-3" style="border-radius: 10px;">
  <div class="card-body py-2 px-2">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <h6 class="fw-bold mb-0" style="font-size:13px;">Lista de Tarifas</h6>
      <button class="btn btn-primary btn-sm" onclick="openModal()" style="background: linear-gradient(135deg, #1a237e, #283593); border: none; font-size:12px;">
        <i class="fas fa-plus me-1"></i>Nueva
      </button>
    </div>
    <div class="table-responsive">
      <table class="table table-hover table-sm mb-0">
        <thead class="table-light">
          <tr>
            <th>Sección</th>
            <th>Tipo</th>
            <th>/Hora</th>
            <th class="d-none d-sm-table-cell">/Día</th>
            <th class="d-none d-md-table-cell">Desde</th>
            <th class="text-center">Acciones</th>
          </tr>
        </thead>
        <tbody id="tbl-tarifas">
          <tr><td colspan="6" class="text-center text-muted">Cargando...</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal Tarifa -->
<div class="modal fade" id="modalTarifa" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius: 12px;">
      <div class="modal-header" style="background: linear-gradient(135deg, #1a237e, #283593); color: white;">
        <h5 class="modal-title fw-bold" id="modalTitle">Nueva Tarifa</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <input type="hidden" id="tar-id">
        <div class="mb-3">
          <label class="form-label fw-bold">Sección</label>
          <select class="form-select" id="tar-seccion" style="border-radius: 8px;">
            <option value="">Seleccionar...</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label fw-bold">Tipo de Vehículo</label>
          <select class="form-select" id="tar-tipo" style="border-radius: 8px;">
            <option value="auto">Auto</option>
            <option value="moto">Moto</option>
            <option value="camioneta">Camioneta</option>
            <option value="bicicleta">Bicicleta</option>
          </select>
        </div>
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Monto por Hora ($)</label>
            <input type="number" class="form-control" id="tar-hora" step="0.50" min="0" placeholder="0.00" style="border-radius: 8px;">
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Monto por Día ($)</label>
            <input type="number" class="form-control" id="tar-dia" step="0.50" min="0" placeholder="0.00" style="border-radius: 8px;">
          </div>
        </div>
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Vigente Desde</label>
            <input type="date" class="form-control" id="tar-fecha" style="border-radius: 8px;">
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Estado</label>
            <select class="form-select" id="tar-activo" style="border-radius: 8px;">
              <option value="1">Activo</option>
              <option value="0">Inactivo</option>
            </select>
          </div>
        </div>
      </div>
      <div class="modal-footer border-0">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" onclick="saveTarifa()" style="background: linear-gradient(135deg, #1a237e, #283593); border: none;">Guardar</button>
      </div>
    </div>
  </div>
</div>

<script>
  var aData = [];
  var aSecciones = [];
  var canDelete = <?php echo tieneRol('admin') ? 'true' : 'false'; ?>;

  function loadTarifas() {
    MsgServer(path.model + 'tarifas.php', function(dat) {
      if (dat.result) {
        aData = dat.data;
        renderTable(dat.data);
      }
    }, { action: 'getall' });
  }

  function loadSecciones() {
    MsgServer(path.model + 'secciones.php', function(dat) {
      if (dat.result) {
        aSecciones = dat.data;
        var html = '<option value="">Seleccionar...</option>';
        dat.data.forEach(function(s) {
          html += '<option value="' + s.id + '">' + s.nombre + '</option>';
        });
        $('#tar-seccion').html(html);
      }
    }, { action: 'getall' });
  }

  function renderTable(data) {
    var html = '';
    if (data.length === 0) {
      html = '<tr><td colspan="6" class="text-center text-muted">No hay tarifas.</td></tr>';
    } else {
      data.forEach(function(r) {
        var icon = r.tipo_vehiculo === 'auto' ? 'fa-car' : r.tipo_vehiculo === 'moto' ? 'fa-motorcycle' : r.tipo_vehiculo === 'camioneta' ? 'fa-truck' : 'fa-bicycle';
        html += '<tr>';
        html += '<td style="font-size:12px;">' + r.seccion_nombre + '</td>';
        html += '<td><i class="fas ' + icon + ' me-1"></i><span class="d-none d-sm-inline">' + r.tipo_vehiculo + '</span></td>';
        html += '<td class="fw-bold">$' + parseFloat(r.monto_hora).toFixed(2) + '</td>';
        html += '<td class="d-none d-sm-table-cell">$' + parseFloat(r.monto_dia).toFixed(2) + '</td>';
        html += '<td class="d-none d-md-table-cell"><small>' + r.vigente_desde + '</small></td>';
        html += '<td class="text-center">';
        html += '<button class="btn btn-sm btn-outline-primary me-1" onclick="editTarifa(' + r.id + ')"><i class="fas fa-edit"></i></button>';
        if (canDelete) {
          html += '<button class="btn btn-sm btn-outline-danger" onclick="deleteTarifa(' + r.id + ')"><i class="fas fa-trash"></i></button>';
        }
        html += '</td></tr>';
      });
    }
    $('#tbl-tarifas').html(html);
  }

  function openModal() {
    $('#tar-id').val('');
    $('#tar-seccion').val('');
    $('#tar-tipo').val('auto');
    $('#tar-hora').val('');
    $('#tar-dia').val('');
    $('#tar-fecha').val(new Date().toISOString().split('T')[0]);
    $('#tar-activo').val('1');
    $('#modalTitle').text('Nueva Tarifa');
    new bootstrap.Modal(document.getElementById('modalTarifa')).show();
  }

  function editTarifa(id) {
    var item = aData.find(function(x){ return x.id == id; });
    if (!item) return;
    $('#tar-id').val(item.id);
    $('#tar-seccion').val(item.seccion_id);
    $('#tar-tipo').val(item.tipo_vehiculo);
    $('#tar-hora').val(item.monto_hora);
    $('#tar-dia').val(item.monto_dia);
    $('#tar-fecha').val(item.vigente_desde);
    $('#tar-activo').val(item.activo);
    $('#modalTitle').text('Editar Tarifa');
    new bootstrap.Modal(document.getElementById('modalTarifa')).show();
  }

  function saveTarifa() {
    var aPar = {
      action: 'save',
      id: $('#tar-id').val(),
      seccion_id: $('#tar-seccion').val(),
      tipo_vehiculo: $('#tar-tipo').val(),
      monto_hora: $('#tar-hora').val(),
      monto_dia: $('#tar-dia').val(),
      vigente_desde: $('#tar-fecha').val(),
      activo: $('#tar-activo').val()
    };
    MsgServer(path.model + 'tarifas.php', function(dat) {
      if (dat.result) {
        MsgNotify("Tarifa guardada", "success");
        bootstrap.Modal.getInstance(document.getElementById('modalTarifa')).hide();
        loadTarifas();
      } else {
        MsgNotify(dat.message || "Error al guardar", "error");
      }
    }, aPar);
  }

  function deleteTarifa(id) {
    Swal.fire({ title: '¿Eliminar tarifa?', text: 'Esta acción no se puede deshacer', icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Eliminar' })
    .then(function(result) {
      if (result.isConfirmed) {
        MsgServer(path.model + 'tarifas.php', function(dat) {
          if (dat.result) { MsgNotify("Tarifa eliminada", "success"); loadTarifas(); }
          else { MsgNotify(dat.message || "Error", "error"); }
        }, { action: 'delete', id: id });
      }
    });
  }

  $(function() { 
    loadSecciones();
    loadTarifas();
  });
</script>

<?php include('view_footer.php'); ?>