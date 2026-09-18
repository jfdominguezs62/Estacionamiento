<?php 
include ( '../constants.php' );
Constants::setpath_root  ("../");
Constants::create_filejs( true );

include ( Constants::getpath_root() . 'config.php' );
include ( Constants::getpath_tweb() . 'core.php' );
include ( Constants::getpath_root() . 'helpers.php' );

include('view_header.php');
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h4 class="fw-bold mb-0"><i class="fas fa-car me-2"></i>Registro Entrada/Salida</h4>
  <input type="text" class="form-control form-control-sm py-1 d-none d-md-block" id="buscar-vehiculo" placeholder="Buscar placa, marca, cajón..." style="max-width:250px; font-size:13px;" oninput="filtrarDentro()">
</div>
<input type="text" class="form-control form-control-sm py-1 mb-2 d-md-none" id="buscar-vehiculo-mobile" placeholder="Buscar placa, marca, cajón..." style="font-size:13px;" oninput="$('#buscar-vehiculo').val(this.value);filtrarDentro();">

<!-- Registro Entrada - Formulario -->
<div class="card border-0 shadow-sm mb-3" style="border-radius: 12px;">
  <div class="card-header text-white fw-bold py-2" style="background: linear-gradient(135deg, #1b5e20, #2e7d32); border-radius: 12px 12px 0 0; font-size: 13px;">
    <i class="fas fa-sign-in-alt me-2"></i>Registrar Entrada
  </div>
  <div class="card-body py-2 px-2">
    <div class="row g-2">
      <div class="col-6 col-md entry-field">
        <label class="form-label fw-bold mb-0" style="font-size:11px;">Sección</label>
        <select class="form-select form-select-sm" id="reg-seccion" onchange="loadTarifaPreview()">
          <option value="">Sección...</option>
        </select>
      </div>
      <div class="col-6 col-md-2 entry-field">
        <label class="form-label fw-bold mb-0" style="font-size:11px;">Cajón</label>
        <input type="text" class="form-control form-control-sm text-uppercase fw-bold" id="reg-cajon" placeholder="A-01" maxlength="10" style="letter-spacing:1px;">
      </div>
      <div class="col-6 col-md entry-field">
        <label class="form-label fw-bold mb-0" style="font-size:11px;">Tipo</label>
        <select class="form-select form-select-sm" id="reg-tipo" onchange="loadTarifaPreview()">
          <option value="auto">Auto</option>
          <option value="moto">Moto</option>
          <option value="camioneta">Camioneta</option>
          <option value="bicicleta">Bicicleta</option>
        </select>
      </div>
      <div class="col-6 col-md-2 entry-field">
        <label class="form-label fw-bold mb-0" style="font-size:11px;">Placa</label>
        <input type="text" class="form-control form-control-sm text-uppercase fw-bold" id="reg-placa" placeholder="ABC-1234" maxlength="10" style="letter-spacing:1px;">
      </div>
      <div class="col-6 col-md entry-field">
        <label class="form-label fw-bold mb-0" style="font-size:11px;">Marca</label>
        <input type="text" class="form-control form-control-sm" id="reg-marca" placeholder="Toyota" maxlength="50">
      </div>
      <div class="col-6 col-md entry-field">
        <label class="form-label fw-bold mb-0" style="font-size:11px;">Color</label>
        <input type="text" class="form-control form-control-sm" id="reg-color" placeholder="Rojo" maxlength="30">
      </div>
      <div class="col-12 col-md-auto entry-field d-flex align-items-end gap-1">
        <span id="tarifa-preview-inline" class="badge bg-info d-none" style="font-size:10px;">$<span id="prev-hora">0</span>/h</span>
        <button class="btn btn-success btn-sm fw-bold flex-grow-1 flex-md-grow-0" onclick="registrarEntrada()" style="border-radius:8px;">
          <i class="fas fa-check-circle me-1"></i>Entrada
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Vehículos Dentro -->
<div class="card border-0 shadow-sm" style="border-radius: 12px;">
  <div class="card-header text-white fw-bold py-2 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #1a237e, #283593); border-radius: 12px 12px 0 0; font-size: 13px;">
    <span><i class="fas fa-parking me-2"></i>Vehículos Dentro</span>
    <span class="badge bg-light text-dark" id="count-dentro">0</span>
  </div>
  <div class="card-body p-0" style="max-height: calc(100vh - 200px); overflow-y: auto;">
    <!-- Desktop table -->
    <table class="table table-hover table-sm mb-0 d-none d-md-table">
      <thead class="table-light" style="position: sticky; top: 0; z-index: 1;">
        <tr>
          <th style="width:60px;">Cajón</th>
          <th style="width:80px;">Placa</th>
          <th>Marca/Color</th>
          <th style="width:80px;">Tipo</th>
          <th>Sección</th>
          <th>Entrada</th>
          <th style="width:70px;">Tiempo</th>
          <th class="text-center" style="width:70px;">Acción</th>
        </tr>
      </thead>
      <tbody id="tbl-dentro">
        <tr><td colspan="8" class="text-center text-muted py-3">Cargando...</td></tr>
      </tbody>
    </table>
    <!-- Mobile cards -->
    <div id="tbl-dentro-mobile" class="d-md-none p-2">
      <div class="text-center text-muted py-3">Cargando...</div>
    </div>
  </div>
</div>

<!-- Modal Salida/Pago -->
<div class="modal fade" id="modalSalida" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-fullscreen-sm-down modal-dialog-centered">
    <div class="modal-content" style="border-radius: 12px;">
      <div class="modal-header text-white py-2" style="background: linear-gradient(135deg, #b71c1c, #c62828); border-radius: 12px 12px 0 0;">
        <h6 class="modal-title fw-bold"><i class="fas fa-sign-out-alt me-2"></i>Registrar Salida</h6>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body py-2 px-3">
        <input type="hidden" id="sal-id">
        <input type="hidden" id="sal-cajon-val">

        <!-- Info del vehículo -->
        <div class="row text-center mb-2 py-1 exit-info-row" style="background:#f8f9fa; border-radius:8px;">
          <div class="col"><small class="text-muted d-block" style="font-size:9px;">Cajón</small><span class="badge bg-dark" id="sal-cajon">-</span></div>
          <div class="col"><small class="text-muted d-block" style="font-size:9px;">Placa</small><span class="fw-bold" id="sal-placa">-</span></div>
          <div class="col d-none d-sm-block"><small class="text-muted d-block" style="font-size:9px;">Marca</small><span id="sal-marca">-</span></div>
          <div class="col d-none d-sm-block"><small class="text-muted d-block" style="font-size:9px;">Color</small><span id="sal-color">-</span></div>
          <div class="col"><small class="text-muted d-block" style="font-size:9px;">Tipo</small><span id="sal-tipo">-</span></div>
          <div class="col"><small class="text-muted d-block" style="font-size:9px;">Entrada</small><span id="sal-entrada">-</span></div>
          <div class="col"><small class="text-muted d-block" style="font-size:9px;">Tiempo</small><span class="badge bg-info" id="sal-tiempo-real">-</span></div>
        </div>

        <!-- Tipo cobro -->
        <div class="row g-2 mb-2">
          <div class="col-12 col-md-4">
            <label class="form-label fw-bold mb-0" style="font-size:11px;">Tipo Cobro</label>
            <div class="btn-group btn-group-sm w-100" role="group">
              <input type="radio" class="btn-check" name="cobro_fraccion" id="cobro-fraccion" value="1" checked onchange="actualizarCobro()">
              <label class="btn btn-outline-success" for="cobro-fraccion" style="font-size:10px;">Fracción=Hora</label>
              <input type="radio" class="btn-check" name="cobro_fraccion" id="cobro-completa" value="0" onchange="actualizarCobro()">
              <label class="btn btn-outline-primary" for="cobro-completa" style="font-size:10px;">Solo Horas</label>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <label class="form-label fw-bold mb-0" style="font-size:11px;">Método Pago</label>
            <select class="form-select form-select-sm" id="sal-metodo">
              <option value="efectivo">Efectivo</option>
              <option value="tarjeta">Tarjeta</option>
              <option value="otro">Otro</option>
            </select>
          </div>
          <div class="col-6 col-md-3">
            <label class="form-label fw-bold mb-0" style="font-size:11px;">Observaciones</label>
            <input type="text" class="form-control form-control-sm" id="sal-obs" placeholder="Opcional">
          </div>
          <div class="col-12 col-md-2 d-flex align-items-end">
            <div class="text-center w-100">
              <small class="text-muted d-block" style="font-size:10px;">TOTAL</small>
              <h4 class="fw-bold text-success mb-0" id="sal-total" style="font-size:22px;">$0.00</h4>
            </div>
          </div>
        </div>

        <!-- Resumen cobro -->
        <div class="row text-center py-1 mb-2" style="background:#e8f5e9; border-radius:8px;">
          <div class="col"><small class="text-muted" style="font-size:10px;">Horas: <strong id="sal-horas" class="text-primary">0</strong></small></div>
          <div class="col"><small class="text-muted" style="font-size:10px;">Tarifa: <strong id="sal-monto-hora">$0</strong></small></div>
          <div class="col"><small class="text-muted" style="font-size:10px;">Concepto: <strong id="sal-concepto">-</strong></small></div>
        </div>

        <!-- Desglose -->
        <div id="sal-desglose" class="px-2 mb-2" style="background:#f8f9fa; border-radius:8px; padding:8px;"></div>
      </div>
      <div class="modal-footer py-2 border-0">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-success btn-sm fw-bold" onclick="confirmarSalida()" style="border-radius:8px;">
          <i class="fas fa-dollar-sign me-1"></i>Cobrar y Salida
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Comprobante -->
<div class="modal fade" id="modalComprobante" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content" style="border-radius: 12px; max-height: 90vh;">
      <div class="modal-header text-white py-2" style="background: linear-gradient(135deg, #1b5e20, #2e7d32); border-radius: 12px 12px 0 0;">
        <h6 class="modal-title fw-bold"><i class="fas fa-receipt me-2"></i>Comprobante de Pago</h6>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-3 text-center" id="comprobante-content" style="overflow-y: auto;">
      </div>
      <div class="modal-footer border-0 justify-content-center py-2 gap-2 flex-wrap">
        <button type="button" class="btn btn-sm btn-outline-danger" onclick="imprimirTicket()" title="Imprimir ticket 80mm">
          <i class="fas fa-print me-1"></i>Imprimir
        </button>
        <button type="button" class="btn btn-sm btn-outline-primary" onclick="descargarPDF()" title="Descargar PDF">
          <i class="fas fa-file-pdf me-1"></i>PDF
        </button>
        <button type="button" class="btn btn-sm btn-outline-success" onclick="enviarCorreo()" title="Enviar por correo">
          <i class="fas fa-envelope me-1"></i>Correo
        </button>
        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<script>
  var aDentro = [];
  var aSecciones = [];
  var datosPago = null;
  var ultimoPago = null;

  function loadSecciones() {
    MsgServer(path.model + 'registros.php', function(dat) {
      if (dat.result) {
        aSecciones = dat.data;
        var html = '<option value="">Seleccionar sección...</option>';
        dat.data.forEach(function(s) {
          html += '<option value="' + s.id + '">' + s.nombre + ' (Cap: ' + s.capacidad + ')</option>';
        });
        $('#reg-seccion').html(html);
      }
    }, { action: 'getsecciones' });
  }

  function loadDentro() {
    MsgServer(path.model + 'registros.php', function(dat) {
      if (dat.result) {
        aDentro = dat.data;
        renderDentro(dat.data);
      }
    }, { action: 'getdentro' });
  }

  function renderDentro(data) {
    $('#count-dentro').text(data.length);
    var filtro = ($('#buscar-vehiculo').val() || '').toUpperCase().trim();
    var html = '';
    var htmlMobile = '';

    if (data.length === 0) {
      html = '<tr><td colspan="8" class="text-center text-muted py-3">No hay vehículos dentro del estacionamiento.</td></tr>';
      htmlMobile = '<div class="text-center text-muted py-3">No hay vehículos dentro.</div>';
    } else {
      var count = 0;
      data.forEach(function(r) {
        var match = !filtro || 
          (r.placa || '').toUpperCase().indexOf(filtro) !== -1 ||
          (r.marca || '').toUpperCase().indexOf(filtro) !== -1 ||
          (r.cajon || '').toUpperCase().indexOf(filtro) !== -1 ||
          (r.color || '').toUpperCase().indexOf(filtro) !== -1;
        if (!match) return;
        count++;
        var icon = r.tipo_vehiculo === 'auto' ? 'fa-car' : r.tipo_vehiculo === 'moto' ? 'fa-motorcycle' : r.tipo_vehiculo === 'camioneta' ? 'fa-truck' : 'fa-bicycle';
        var marcaColor = (r.marca || '-') + ' / ' + (r.color || '-');

        // Desktop row
        html += '<tr>';
        html += '<td><span class="badge bg-dark">' + r.cajon + '</span></td>';
        html += '<td class="fw-bold" style="letter-spacing: 1px;">' + r.placa + '</td>';
        html += '<td><small>' + marcaColor + '</small></td>';
        html += '<td><i class="fas ' + icon + ' me-1"></i>' + r.tipo_vehiculo + '</td>';
        html += '<td>' + r.seccion_nombre + '</td>';
        html += '<td><small>' + r.fecha_entrada + '</small></td>';
        html += '<td><span class="badge bg-info">' + r.tiempo_transcurrido + '</span></td>';
        html += '<td class="text-center"><button class="btn btn-sm btn-danger" onclick="openSalida(' + r.id + ')"><i class="fas fa-sign-out-alt"></i></button></td></tr>';

        // Mobile card
        htmlMobile += '<div class="card mb-2 border" style="border-radius:10px!important;font-size:12px;">';
        htmlMobile += '<div class="card-body py-2 px-2">';
        htmlMobile += '<div class="d-flex justify-content-between align-items-center mb-1">';
        htmlMobile += '<span class="badge bg-dark" style="font-size:13px;">' + r.cajon + '</span>';
        htmlMobile += '<span class="fw-bold" style="letter-spacing:1px;font-size:14px;">' + r.placa + '</span>';
        htmlMobile += '<span class="badge bg-info" style="font-size:10px;">' + r.tiempo_transcurrido + '</span>';
        htmlMobile += '</div>';
        htmlMobile += '<div class="d-flex justify-content-between align-items-center">';
        htmlMobile += '<div><small class="text-muted"><i class="fas ' + icon + ' me-1"></i>' + r.tipo_vehiculo + ' · ' + r.seccion_nombre + '</small>';
        if (r.marca || r.color) htmlMobile += '<br><small class="text-muted">' + marcaColor + '</small>';
        htmlMobile += '</div>';
        htmlMobile += '<button class="btn btn-sm btn-danger" onclick="openSalida(' + r.id + ')" style="font-size:11px;"><i class="fas fa-sign-out-alt me-1"></i>Salida</button>';
        htmlMobile += '</div></div></div>';
      });
      if (count === 0) {
        html = '<tr><td colspan="8" class="text-center text-muted py-3">No se encontró "' + filtro + '"</td></tr>';
        htmlMobile = '<div class="text-center text-muted py-3">No se encontró "' + filtro + '"</div>';
      }
    }
    $('#tbl-dentro').html(html);
    $('#tbl-dentro-mobile').html(htmlMobile);
  }

  function filtrarDentro() {
    renderDentro(aDentro);
  }

  function loadTarifaPreview() {
    var seccion = $('#reg-seccion').val();
    var tipo = $('#reg-tipo').val();
    if (!seccion || !tipo) {
      $('#tarifa-preview-inline').addClass('d-none');
      return;
    }
    MsgServer(path.model + 'registros.php', function(dat) {
      if (dat.result) {
        $('#prev-hora').text(parseFloat(dat.data.monto_hora).toFixed(2));
        $('#tarifa-preview-inline').removeClass('d-none');
      } else {
        $('#tarifa-preview-inline').addClass('d-none');
      }
    }, { action: 'gettarifa', seccion_id: seccion, tipo_vehiculo: tipo });
  }

  function registrarEntrada() {
    var seccion = $('#reg-seccion').val();
    var cajon = $('#reg-cajon').val().trim();
    var tipo = $('#reg-tipo').val();
    var placa = $('#reg-placa').val().trim();
    var marca = $('#reg-marca').val().trim();
    var color = $('#reg-color').val().trim();

    if (!seccion) { MsgNotify("Seleccione una sección", "error"); return; }
    if (!cajon) { MsgNotify("Ingrese el número de cajón", "error"); return; }
    if (!placa) { MsgNotify("Ingrese la placa del vehículo", "error"); return; }

    MsgServer(path.model + 'registros.php', function(dat) {
      if (dat.result) {
        MsgNotify(dat.message, "success");
        $('#reg-placa').val('');
        $('#reg-cajon').val('');
        $('#reg-marca').val('');
        $('#reg-color').val('');
        $('#tarifa-preview-inline').addClass('d-none');
        loadDentro();
      } else {
        MsgNotify(dat.message || "Error al registrar entrada", "error");
      }
    }, { action: 'registrar_entrada', seccion_id: seccion, cajon: cajon, tipo_vehiculo: tipo, placa: placa, marca: marca, color: color });
  }

  function openSalida(id) {
    var item = aDentro.find(function(x){ return x.id == id; });
    if (!item) return;

    $('#sal-id').val(id);
    $('#sal-cajon-val').val(item.cajon);
    $('#sal-cajon').text(item.cajon);
    $('#sal-placa').text(item.placa);
    $('#sal-marca').text(item.marca || '-');
    $('#sal-color').text(item.color || '-');
    $('#sal-tipo').text(item.tipo_vehiculo);
    $('#sal-entrada').text(item.fecha_entrada);
    $('#sal-tiempo-real').text(item.tiempo_transcurrido);
    $('#cobro-fraccion').prop('checked', true);

    // Calcular pago
    MsgServer(path.model + 'registros.php', function(dat) {
      if (dat.result) {
        datosPago = dat.data;
        actualizarCobro();
      }
    }, { action: 'calcular_pago', id: id });

    new bootstrap.Modal(document.getElementById('modalSalida')).show();
  }

  function actualizarCobro() {
    if (!datosPago) return;
    if (datosPago.monto_hora == 0) {
      $('#sal-horas').text('-');
      $('#sal-monto-hora').text('$0.00');
      $('#sal-concepto').text('SIN TARIFA CONFIGURADA');
      $('#sal-total').text('$0.00');
      $('#sal-desglose').html('');
      MsgNotify("No hay tarifa configurada para esta sección y tipo de vehículo. Cree una en Tarifas.", "error");
      return;
    }
    var tipo = $('input[name="cobro_fraccion"]:checked').val();
    var op = tipo == 1 ? datosPago.opcion_fraccion : datosPago.opcion_completa;

    $('#sal-horas').text(op.horas + 'h');
    $('#sal-monto-hora').text('$' + parseFloat(datosPago.monto_hora).toFixed(2));
    $('#sal-concepto').text(op.descripcion);
    $('#sal-total').text('$' + parseFloat(op.monto).toFixed(2));

    // Desglose detallado
    var html = '';
    html += '<table class="table table-sm mb-0" style="font-size:12px;">';
    if (op.dias > 0) {
      html += '<tr><td>' + op.dias + ' día(s) × $' + parseFloat(datosPago.monto_dia).toFixed(2) + '</td><td class="text-end fw-bold">$' + parseFloat(op.monto_dias).toFixed(2) + '</td></tr>';
    }
    if (op.horas_resto > 0) {
      html += '<tr><td>' + op.horas_resto + ' hora(s) × $' + parseFloat(datosPago.monto_hora).toFixed(2) + '</td><td class="text-end fw-bold">$' + parseFloat(op.monto_horas).toFixed(2) + '</td></tr>';
    }
    if (op.dias === 0) {
      html += '<tr><td>' + op.horas + ' hora(s) × $' + parseFloat(datosPago.monto_hora).toFixed(2) + '</td><td class="text-end fw-bold">$' + parseFloat(op.monto).toFixed(2) + '</td></tr>';
    }
    html += '</table>';
    $('#sal-desglose').html(html);
  }

  function confirmarSalida() {
    var id = $('#sal-id').val();
    var metodo = $('#sal-metodo').val();
    var obs = $('#sal-obs').val();
    var cobroFraccion = $('input[name="cobro_fraccion"]:checked').val();

    MsgServer(path.model + 'registros.php', function(dat) {
      if (dat.result) {
        bootstrap.Modal.getInstance(document.getElementById('modalSalida')).hide();
        
        // Mostrar comprobante
        var tipoCobro = dat.cobro_fraccion == 1 ? 'Fracción como hora completa' : 'Solo horas completas';
        var html = '';
        html += '<div class="card border-0" style="border-radius: 12px; background: #f8f9fa;">';
        html += '<div class="card-body p-4">';
        html += '<h4 class="fw-bold text-success mb-3">¡Pago Realizado!</h4>';
        html += '<div class="row mb-2"><div class="col-6 text-end text-muted">Recibo:</div><div class="col-6 text-start fw-bold">' + dat.recibo + '</div></div>';
        html += '<div class="row mb-2"><div class="col-6 text-end text-muted">Cajón:</div><div class="col-6 text-start fw-bold">' + $('#sal-cajon-val').val() + '</div></div>';
        html += '<div class="row mb-2"><div class="col-6 text-end text-muted">Placa:</div><div class="col-6 text-start fw-bold">' + dat.placa + '</div></div>';
        html += '<div class="row mb-2"><div class="col-6 text-end text-muted">Marca:</div><div class="col-6 text-start">' + (dat.marca || '-') + '</div></div>';
        html += '<div class="row mb-2"><div class="col-6 text-end text-muted">Color:</div><div class="col-6 text-start">' + (dat.color || '-') + '</div></div>';
        html += '<div class="row mb-2"><div class="col-6 text-end text-muted">Tipo:</div><div class="col-6 text-start">' + dat.tipo_vehiculo + '</div></div>';
        html += '<div class="row mb-2"><div class="col-6 text-end text-muted">Entrada:</div><div class="col-6 text-start">' + dat.fecha_entrada + '</div></div>';
        html += '<div class="row mb-2"><div class="col-6 text-end text-muted">Salida:</div><div class="col-6 text-start">' + dat.fecha_salida + '</div></div>';
        html += '<div class="row mb-2"><div class="col-6 text-end text-muted">Tiempo real:</div><div class="col-6 text-start">' + dat.tiempo_real + '</div></div>';
        html += '<div class="row mb-2"><div class="col-6 text-end text-muted">Horas cobro:</div><div class="col-6 text-start fw-bold">' + dat.horas + 'h</div></div>';
        html += '<div class="row mb-2"><div class="col-6 text-end text-muted">Tipo cobro:</div><div class="col-6 text-start"><small>' + tipoCobro + '</small></div></div>';
        html += '<hr>';
        // Desglose detallado
        html += '<table class="table table-sm mb-2" style="font-size:13px;">';
        if (dat.dias_cobro > 0) {
          html += '<tr><td>' + dat.dias_cobro + ' día(s) × $' + parseFloat(dat.monto_dia).toFixed(2) + '</td><td class="text-end fw-bold">$' + parseFloat(dat.dias_cobro * dat.monto_dia).toFixed(2) + '</td></tr>';
        }
        if (dat.horas_resto > 0) {
          html += '<tr><td>' + dat.horas_resto + ' hora(s) × $' + parseFloat(dat.monto_hora).toFixed(2) + '</td><td class="text-end fw-bold">$' + parseFloat(dat.horas_resto * dat.monto_hora).toFixed(2) + '</td></tr>';
        }
        if (dat.dias_cobro === 0) {
          html += '<tr><td>' + dat.horas + ' hora(s) × $' + parseFloat(dat.monto_hora).toFixed(2) + '</td><td class="text-end fw-bold">$' + parseFloat(dat.monto).toFixed(2) + '</td></tr>';
        }
        html += '</table>';
        html += '<h3 class="fw-bold text-success">TOTAL: $' + parseFloat(dat.monto).toFixed(2) + '</h3>';
        html += '</div></div>';
        
        $('#comprobante-content').html(html);
        new bootstrap.Modal(document.getElementById('modalComprobante')).show();

        ultimoPago = dat;
        ultimoPago.cajon = $('#sal-cajon-val').val();
        loadDentro();
        $('#sal-obs').val('');
        datosPago = null;
      } else {
        MsgNotify(dat.message || "Error al registrar salida", "error");
      }
    }, { action: 'registrar_salida', id: id, metodo_pago: metodo, observaciones: obs, cobro_fraccion: cobroFraccion });
  }

  function descargarPDF() {
    if (!ultimoPago) return;
    var d = ultimoPago;
    var tipoCobro = d.cobro_fraccion == 1 ? 'Fracción=Hora' : 'Solo Horas';
    var docDefinition = {
      pageSize: { width: 226.77, height: 'auto' },
      pageMargins: [15, 15, 15, 15],
      content: [
        { text: 'ESTACIONAMIENTO', style: { fontSize: 11, bold: true, alignment: 'center' } },
        { text: 'Comprobante de Pago', style: { fontSize: 9, alignment: 'center', margin: [0, 2, 0, 8] } },
        { canvas: [{ type: 'line', x1: 0, y1: 0, x2: 196, y2: 0, lineWidth: 0.5 }], margin: [0, 0, 0, 6] },
        { columns: [{ text: 'Recibo:', width: 55, style: { fontSize: 8 } }, { text: d.recibo, style: { fontSize: 8, bold: true } }] },
        { columns: [{ text: 'Fecha:', width: 55, style: { fontSize: 8 } }, { text: d.fecha_salida, style: { fontSize: 8 } }] },
        { columns: [{ text: 'Cajón:', width: 55, style: { fontSize: 8 } }, { text: d.cajon, style: { fontSize: 8, bold: true } }] },
        { columns: [{ text: 'Placa:', width: 55, style: { fontSize: 8 } }, { text: d.placa, style: { fontSize: 8, bold: true } }] },
        { columns: [{ text: 'Marca:', width: 55, style: { fontSize: 8 } }, { text: (d.marca || '-') + ' / ' + (d.color || '-'), style: { fontSize: 8 } }] },
        { columns: [{ text: 'Tipo:', width: 55, style: { fontSize: 8 } }, { text: d.tipo_vehiculo, style: { fontSize: 8 } }] },
        { columns: [{ text: 'Entrada:', width: 55, style: { fontSize: 8 } }, { text: d.fecha_entrada, style: { fontSize: 8 } }] },
        { columns: [{ text: 'Salida:', width: 55, style: { fontSize: 8 } }, { text: d.fecha_salida, style: { fontSize: 8 } }] },
        { columns: [{ text: 'Tiempo:', width: 55, style: { fontSize: 8 } }, { text: d.tiempo_real, style: { fontSize: 8 } }] },
        { columns: [{ text: 'Tipo Cobro:', width: 55, style: { fontSize: 8 } }, { text: tipoCobro, style: { fontSize: 8 } }] },
        { canvas: [{ type: 'line', x1: 0, y1: 0, x2: 196, y2: 0, lineWidth: 0.5 }], margin: [0, 6, 0, 6] },
        { text: 'TOTAL: $' + parseFloat(d.monto).toFixed(2), style: { fontSize: 13, bold: true, alignment: 'center' } },
        { canvas: [{ type: 'line', x1: 0, y1: 0, x2: 196, y2: 0, lineWidth: 0.5 }], margin: [0, 6, 0, 6] },
        { text: 'Gracias por su preferencia', style: { fontSize: 7, alignment: 'center', margin: [0, 4, 0, 0] } }
      ]
    };
    pdfMake.createPdf(docDefinition).download('comprobante_' + d.recibo + '.pdf');
  }

  function imprimirTicket() {
    if (!ultimoPago) return;
    var d = ultimoPago;
    var tipoCobro = d.cobro_fraccion == 1 ? 'Fracción=Hora' : 'Solo Horas';
    var win = window.open('', '_blank', 'width=300,height=600');
    win.document.write('<html><head><title>Ticket</title>');
    win.document.write('<style>@page{size:80mm auto;margin:2mm;body{font-family:monospace;font-size:10px;margin:0;padding:3mm;}h2{font-size:11px;text-align:center;margin:2px 0;}table{width:100%;font-size:9px;}td{padding:1px 0;}.tot{font-size:12px;font-weight:bold;text-align:center;border-top:1px dashed #000;padding-top:3px;margin-top:3px;}.line{border-top:1px dashed #000;margin:3px 0;}</style></head><body>');
    win.document.write('<h2>ESTACIONAMIENTO</h2>');
    win.document.write('<div class="line"></div>');
    win.document.write('<table>');
    win.document.write('<tr><td>Recibo:</td><td style="text-align:right;font-weight:bold;">' + d.recibo + '</td></tr>');
    win.document.write('<tr><td>Fecha:</td><td style="text-align:right;">' + d.fecha_salida + '</td></tr>');
    win.document.write('<tr><td>Cajón:</td><td style="text-align:right;font-weight:bold;">' + d.cajon + '</td></tr>');
    win.document.write('<tr><td>Placa:</td><td style="text-align:right;font-weight:bold;">' + d.placa + '</td></tr>');
    win.document.write('<tr><td>Marca/Color:</td><td style="text-align:right;">' + (d.marca||'-') + ' / ' + (d.color||'-') + '</td></tr>');
    win.document.write('<tr><td>Tipo:</td><td style="text-align:right;">' + d.tipo_vehiculo + '</td></tr>');
    win.document.write('<tr><td>Entrada:</td><td style="text-align:right;">' + d.fecha_entrada + '</td></tr>');
    win.document.write('<tr><td>Salida:</td><td style="text-align:right;">' + d.fecha_salida + '</td></tr>');
    win.document.write('<tr><td>Tiempo:</td><td style="text-align:right;">' + d.tiempo_real + '</td></tr>');
    win.document.write('<tr><td>Tipo Cobro:</td><td style="text-align:right;">' + tipoCobro + '</td></tr>');
    win.document.write('</table>');
    win.document.write('<div class="line"></div>');
    win.document.write('<div class="tot">TOTAL: $' + parseFloat(d.monto).toFixed(2) + '</div>');
    win.document.write('<div class="line"></div>');
    win.document.write('<p style="text-align:center;font-size:7px;margin-top:3px;">Gracias por su preferencia</p>');
    win.document.write('</body></html>');
    win.document.close();
    setTimeout(function() { win.print(); }, 500);
  }

  function enviarCorreo() {
    if (!ultimoPago) return;
    var email = prompt('Ingrese el correo electrónico para enviar el comprobante:');
    if (!email || email.trim() === '') return;
    MsgServer(path.model + 'email.php', function(dat) {
      if (dat.result) {
        MsgNotify("Comprobante enviado a " + email, "success");
      } else {
        MsgNotify(dat.message || "Error al enviar correo", "error");
      }
    }, { action: 'send_recibo', datos: JSON.stringify(ultimoPago), email: email });
  }

  $(function() { 
    console.log('DEBUG: Iniciando loadSecciones...');
    loadSecciones();
    loadDentro();
    setInterval(loadDentro, 60000);
  });
</script>

<?php include('view_footer.php'); ?>