$(function() {
  $("html").removeAttr("hidden");
  var params = {};
  window.addEventListener("beforeinstallprompt", e => {
    // ก่อน Chrome 67 ยังต้อง prevent default ไว้ก่อน
    e.preventDefault();
    // เรียก prompt() เพื่อให้ dialog
    e.prompt();
  });
  $(".submitx").click(function() {
    var $this = $(this);
    $.post("./src/transfer.php", params, function(data) {
      data = JSON.parse(data);
      $("#verify-modal").modal("toggle");
      if (data.error.code != 100) {
        Swal.fire("Oops...", data.error.msg_th, "error");
        return;
      }
      Swal.fire("Good job!", "โอนเงินสำเร็จ", "success");
      location.reload();
    });
  });
  $(".et").click(function() {
    var $this = $(this);
    $(".et").removeClass("active");
    $this.toggleClass("active");
    $("input[name='bankID']").val($this.data("id"));
  });
  $("#verify").submit(function(e) {
    e.preventDefault();
    var sa = $(this).serializeArray();
    params = {};
    for (const key in sa) {
      if (sa.hasOwnProperty(key)) {
        var e = sa[key];
        params[e.name] = e.value;
      }
    }
    $.post("./src/verify.php", params, function(data) {
      data = JSON.parse(data);
      if (data.error.code != 100) {
        Swal.fire("Oops...", data.error.msg_th, "error");
        return;
      }
      var d = data.result;
      var htm = "";
      htm += `<li>จาก : ${d.accountFromName} (บัญชีนี้)</li>`;
      htm += `<li>ถึง : ${d.accountToName} (${d.accountTo})</li>`;
      htm += `<li>จำนวน : ${params["amount"]} บาท</li>`;
      var $vf = $("#verify-form");
      $vf.find("ul").html(htm);
      $vf.find("img").attr("src", $(`#b${params["bankID"]}`).attr("src"));
      $("#verify-modal").modal("show");
    });
  });
});
