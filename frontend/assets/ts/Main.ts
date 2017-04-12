import * as $ from "jquery";
import {SMSList} from "./classes/SMSList";
import {Gateways} from "./classes/Gateways";
import {Templates} from "./classes/Templates";
$(function(){
	SMSList.initIfNeeded();
	Gateways.initIfNeeded();
	Templates.initIfNeeded();
});