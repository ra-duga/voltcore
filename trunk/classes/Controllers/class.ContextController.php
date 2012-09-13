<?php
class ContextController extends AbstractController{
	
    /**
     * Обработка по умолчанию.
     */
    protected function getIndexInfo(){
        Registry::getResponse()->show404();
    }
}