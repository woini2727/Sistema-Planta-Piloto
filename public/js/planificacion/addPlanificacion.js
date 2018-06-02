$(document).ready(function(){
	$("#selectProductos").hide();
	$("#selectInsumos").hide();

		$(".agregarProducto").click(function(){
			
			var td1=document.createElement("td");
			var td2=document.createElement("td");
			var td3=document.createElement("td");
			var td4=document.createElement("td");
			var td5=document.createElement("td");
			td1.setAttribute('id','codigo');
			td2.setAttribute('id','nombre');
			td3.setAttribute('id','cantidad');
			td4.setAttribute('id','tp');


			//var input1=$("<input>").attr({type:'text',class:'interes'}).appendTo(td1);
			var input3=$("<input>").attr({type:'text',class:'interes'}).appendTo(td3);

			//SELECT tipo tp
			var input4=$("<select>").attr({type:'text',class:'interes',id:'selecttp'}).appendTo(td4);
			var option1=$("<option>NO</option>").appendTo(input4);
			var option2=$("<option>SI</option>").appendTo(input4);
			
			//select de los productos
			var select=$("#selectProductos").clone().appendTo(td2);
			select.attr('id','productos');
			select.addClass('inte');
			select.show();
			

			var guardar=document.createElement('img');
			guardar.src=$('img#iHGuardar').attr('src');
			guardar.setAttribute('width','30px');
			guardar.setAttribute('height','30px');
			guardar.setAttribute('class','guardar');
			/*var guardar=$('img#iHGuardar').clone();
			guardar.show();
			console.log(guardar);*/
			td5.appendChild(guardar);

			row = $(this).closest('tr');
			$('.nuevaLineaProducto:last').append(td1,td2,td3,td4,td5);
			
			tbody = $(this).closest('tbody');
			var tr=document.createElement("tr");
			tr.setAttribute('class','nuevaLineaProducto');
			//$(this).remove();
			var parent=$(this).closest('tr');
			tbody.append(tr,this);
			parent.remove();
			//$('.tbodyPlanif').append();
		});
		$(".agregarInsumo").click(function(){
			
			var td1=document.createElement("td");
			var td2=document.createElement("td");
			var td3=document.createElement("td");
			//var td4=document.createElement("td");
			var td5=document.createElement("td");

			//var input1=$("<input>").attr({type:'text',class:'interes'}).appendTo(td1);
			var input3=$("<input>").attr({type:'text',class:'inte'}).appendTo(td3);
			//select de los Insumos
			var select=$("#selectInsumos").clone().appendTo(td2);
			select.attr('id','insumos');
			select.addClass('inte');
			select.show();
			//var input4=$("<input>").attr({type:'text',class:'interes'}).appendTo(td4);
			var guardar=document.createElement('img');
			guardar.src=$('img#iHGuardar').attr('src');
			guardar.setAttribute('width','30px');
			guardar.setAttribute('height','30px');
			guardar.setAttribute('class','guardar');
			td5.appendChild(guardar);

			row = $(this).closest('tr');
			$('.nuevaLineaInsumo:last').append(td1,td2,td3,td5);
			
			tbody = $(this).closest('tbody');
			var tr=document.createElement("tr");
			tr.setAttribute('class','nuevaLineaInsumo');
			//$(this).remove();
			var parent=$(this).closest('tr');
			tbody.append(tr,this);
			parent.remove();

			//$('.tbodyPlanif').append();

		});
		//cuando seleccionoel producto pongo automaticamente el codigo
		$("body").on('change','#productos',function(){   //cambiar el codigo dependiendo el producto
		//$('option:selected', this).attr('mytag');
			var codigo=$('option:selected', this).data('codigo');
			console.log(codigo);
			$(this).parent('td').prev('td').text(codigo);

	});
		$("body").on('change','#insumos',function(){   //cambiar el codigo dependiendo el insumo
		//$('option:selected', this).attr('mytag');
			var codigo=$('option:selected', this).data('codigo');
			console.log(codigo);
			$(this).parent('td').prev('td').text(codigo);

	});
	});
