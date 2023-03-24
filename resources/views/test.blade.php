<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="utf-8" />
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">


   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.6.1/font/bootstrap-icons.css">
   <link rel="stylesheet" type="text/css" media="screen" href="{{ asset('assets/css/ui.jqgrid-bootstrap5.css') }}" />
   <script type="text/ecmascript" src="{{ asset('assets/js/jquery.jqGrid.min.js') }}"></script>
   <script type="text/ecmascript" src="{{ asset('assets/js/jquery.min.js') }}"></script>
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>


<script>
$.jgrid.defaults.styleUI = 'Bootstrap5';
$.jgrid.defaults.iconSet = 'Bootstrap5';

//$.jgrid.defaults.iconSet = "Iconic";
//$.jgrid.defaults.iconSet = "fontAwesome";
</script>
</head>
<body>
    <table id="jqGrid"></table>
    <div id="jqGridPager"></div>

    <script>
        var dataArray = [
            {clid: 1, name: 'Bob', phone: '232-532-6268', birthday: "01/01/1971"},
            {clid: 2, name: 'Jeff', phone: '365-267-8325', birthday: "02/02/1972"}
        ];

        $("#jqGrid").jqGrid({
            datatype: 'local',
            data: dataArray,
            colModel: [
                {search:true, name: 'clid', label : 'Id', sorttype: "int"},
                {search:true, name: 'name', label : 'Name'},
                {search:true, name: 'phone', label : 'Phone Number'},
                {search:true, name: 'birthday', label : 'Birth day', sorttype: 'date', datefmt:'d/m/Y'},
            ],
            autowidth: true,
            shrinkToFit: true,
            height: 250,
            rowNum: 10,
        });

        $('#jqGrid').jqGrid('filterToolbar');
			$('#jqGrid').jqGrid('navGrid',"#jqGridPager", {
                search: false, // show search button on the toolbar
                add: false,
                edit: false,
                del: false,
                refresh: true
            });

    </script>
</body>
</html>
