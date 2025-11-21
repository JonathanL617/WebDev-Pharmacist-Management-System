let confirmMode="";

function openConfirm(id,mode){
    confirmMode=mode;
    document.getElementById("confirmOrderId").value=id;
    document.getElementById("confirmTitle").innerHTML=(mode=="approve")?"Approve Order":(mode=="reject")?"Reject Order":"Mark as Done";
    document.getElementById("confirmMsg").innerHTML="";
    new bootstrap.Modal(document.getElementById("confirmModal")).show();
}

document.getElementById("confirmBtn").onclick=function(){
    let id=document.getElementById("confirmOrderId").value;
    let approver = loggedInUserId; // always use the logged-in user
    let comment=document.getElementById("confirmComment").value;

    fetch("controller.php",{
        method:"POST",
        headers:{"Content-Type":"application/json"},
        body:JSON.stringify({action:confirmMode,order_id:id,approver_id:approver,comment:comment})
    })
    .then(r=>r.json())
    .then(d=>{
        if(!d.success){
            document.getElementById("confirmMsg").innerHTML=`<div class='alert alert-danger'>${d.msg}</div>`;
            return;
        }
        alert("Order " + confirmMode + " successfully!");
        loadOrders();
        new bootstrap.Modal(document.getElementById("confirmModal")).hide();
    });
}

function viewOrder(id){
    document.getElementById("viewModalBody").innerHTML="Loading...";
    new bootstrap.Modal(document.getElementById("viewModal")).show();

    fetch("controller.php",{
        method:"POST",
        headers:{"Content-Type":"application/json"},
        body:JSON.stringify({action:"get_details",order_id:id})
    })
    .then(r=>r.json())
    .then(d=>{
        if(!d.success){ document.getElementById("viewModalBody").innerHTML=d.msg; return; }
        let o=d.order, det=d.details;
        let html=`<strong>Order:</strong> ${o.order_id}<br>
                  <strong>Date:</strong> ${o.order_date}<br>
                  <strong>Patient:</strong> ${o.patient_name}<br>
                  <strong>Doctor:</strong> ${o.staff_name}<br>
                  <strong>Status:</strong> ${o.status_id}<hr>
                  <table class="table table-bordered"><tr><th>Medicine</th><th>Qty</th><th>Price</th><th>Stock</th><th>Subtotal</th></tr>`;
        let total=0;
        det.forEach(x=>{
            let sub=x.ordered_qty*x.medicine_price;
            total+=sub;
            html+=`<tr><td>${x.medicine_name}</td><td>${x.ordered_qty}</td><td>${x.medicine_price}</td><td>${x.stock_qty}</td><td>${sub.toFixed(2)}</td></tr>`;
        });
        html+=`</table><div class="text-end"><strong>Total: RM ${total.toFixed(2)}</strong></div>`;
        document.getElementById("viewModalBody").innerHTML=html;
    });
}

function loadOrders(filter=''){
    fetch('controller.php',{
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body:JSON.stringify({action:'list_orders',filter})
    })
    .then(r=>r.json())
    .then(d=>{
        let tbody=document.getElementById('ordersTable');
        tbody.innerHTML='';
        if(!d.success || d.orders.length===0){
            tbody.innerHTML="<tr><td colspan='6' class='text-center'>No orders found</td></tr>";
            return;
        }
        d.orders.forEach(o=>{
            let statusClass=o.status_id.toLowerCase()==='approved'?'bg-success':
                            o.status_id.toLowerCase()==='rejected'?'bg-danger':
                            o.status_id.toLowerCase()==='done'?'bg-info text-dark':'bg-warning text-dark';
            let row=document.createElement('tr');
            row.innerHTML=`
                <td>${o.order_id}</td>
                <td>${o.order_date}</td>
                <td>${o.patient_name}</td>
                <td>${o.staff_name}</td>
                <td><span class="badge ${statusClass}">${o.status_id}</span></td>
                <td>
                    <button class="btn btn-primary btn-sm" onclick="viewOrder('${o.order_id}')">View</button>
                    <button class="btn btn-success btn-sm" onclick="openConfirm('${o.order_id}','approve')">Approve</button>
                    <button class="btn btn-danger btn-sm" onclick="openConfirm('${o.order_id}','reject')">Reject</button>
                    <button class="btn btn-info btn-sm" onclick="openConfirm('${o.order_id}','done')">Done</button>
                </td>`;
            tbody.appendChild(row);
        });
    });
}

// Load on page ready
document.addEventListener('DOMContentLoaded',()=>loadOrders());
