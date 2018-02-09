<?php

class LocalDispatch_List_View extends Vtiger_Index_View
{
    public function process(Vtiger_Request $request)
    {
        $viewer = $this->getViewer($request);
        $viewer->view('List.tpl', $request->getModule());
    }
}
