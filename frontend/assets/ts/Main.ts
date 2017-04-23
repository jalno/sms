import * as $ from "jquery";
import {SMSList} from "./classes/SMSList";
import {Gateways} from "./classes/Gateways";
import {Templates} from "./classes/Templates";
import Send from "./classes/Send";
$(function(){
	SMSList.initIfNeeded();
	Gateways.initIfNeeded();
	Templates.initIfNeeded();
	Send.initIfNeeded();
});