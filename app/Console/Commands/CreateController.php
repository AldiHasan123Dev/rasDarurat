<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateController extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'controller:model {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $model = $this->argument('name');

        $content = "<?php\r\n\r\nnamespace App\\Http\\Controllers;\r\n\r\nuse App\\Models\\".$model.";\r\nuse Illuminate\\Http\\Request;\r\nuse Illuminate\\Support\\Facades\\Hash;\r\nuse Yajra\\Datatables\\Datatables;\r\n\r\nclass ".$model."Controller extends Controller\r\n{\r\n    public function index()\r\n    {\r\n        return view('admin.".strtolower($model).".index');\r\n    }\r\n\r\n    public function store(Request ".'$request'.")\r\n    {\r\n              ".'$data'." = ".'$request'."->all();\r\n        ".$model."::create(".'$data'.");\r\n\r\n        return back()->with('success','Data berhasil disimpan');\r\n    }\r\n\r\n    public function update(".$model." $".strtolower($model).", Request ".'$request'.")\r\n    {\r\n        ".'$data'." = ".'$request'."->all();\r\n        $".strtolower($model)."->update(".'$data'.");\r\n\r\n        return back()->with('success','Data berhasil diupdate');\r\n    }\r\n\r\n    public function destroy(".$model." $".strtolower($model).")\r\n    {\r\n        $".strtolower($model)."->delete();\r\n\r\n        return back()->with('success','Data berhasil dihapus');\r\n    }\r\n\r\n    public function datatable()\r\n    {\r\n        ".'$data'." = ".$model."::all()->sortByDesc('created_at');\r\n\r\n        return Datatables::of(".'$data'.")\r\n            ->addColumn('action', function (".'$data'.") {\r\n                ".'$view'." = view('admin.".strtolower($model).".form',['".strtolower($model)."'=>".'$data'."])->render();\r\n                ".'$html'." = '<div class=\"d-flex gap-1\">\r\n                            <form action=\"'.route('".strtolower($model).".destroy',".'$data'.").'\" method=\"post\">\r\n                                <input type=\"hidden\" name=\"_token\" value=\"'.csrf_token().'\" />\r\n                                <input type=\"hidden\" name=\"_method\" value=\"delete\" />\r\n                                <button type=\"submit\" onclick=\"return confirm(\\'Are you sure?\\')\" class=\"no-attr text-danger\" data-bs-toggle=\"tooltip\" data-bs-placement=\"top\" title=\"Hapus\"><i class=\"fas fa-trash\"></i></button>\r\n                            </form>\r\n                            <button class=\"no-attr text-primary\" title=\"Edit\" data-bs-toggle=\"offcanvas\" data-bs-target=\"#offcanvas".$model."Update'.".'$data'."->id.'\" aria-controls=\"offcanvas".$model."Update'.".'$data'."->id.'\"><i class=\"fas fa-pencil\"></i></button>\r\n                        </div>\r\n\r\n                        <div class=\"offcanvas offcanvas-end\" tabindex=\"-1\" id=\"offcanvas".$model."Update'.".'$data'."->id.'\" aria-labelledby=\"offcanvas".$model."Update'.".'$data'."->id.'Label\">\r\n                            <div class=\"offcanvas-header\">\r\n                                <h5 class=\"offcanvas-title\" id=\"offcanvas".$model."Update'.".'$data'."->id.'Label\">Form ".$model."</h5>\r\n                                <button type=\"button\" class=\"btn-close text-reset\" data-bs-dismiss=\"offcanvas\" aria-label=\"Close\"></button>\r\n                            </div>\r\n                            <div class=\"offcanvas-body\">\r\n                                <form action=\"'.route('".strtolower($model).".update',".'$data'.").'\" method=\"post\">\r\n                                <input type=\"hidden\" name=\"_token\" value=\"'.csrf_token().'\" />\r\n                                    <input type=\"hidden\" name=\"_method\" value=\"PUT\" />\r\n                                    '.".'$view'.".'\r\n                                </form>\r\n                            </div>\r\n                        </div>';\r\n                return ".'$html'.";\r\n            })\r\n            ->rawColumns(['action'])\r\n            ->make(true);\r\n    }\r\n}\r\n";

        $fp = fopen(base_path('app')."/"."Http/Controllers/".$model."Controller.php","wb");
        fwrite($fp,$content);
        fclose($fp);
    }
}
