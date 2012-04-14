<div class="mainContent">
    <div class="pageTitle">异常： <{$this->message}>
    </div>
<{if isset($this->exception)}>
    <div class="exception">
    <h3>错误:</h3>
    <pre>
        <{$this->exception->getMessage()}>
    </pre>
    <h3>堆栈:</h3>
    <pre><{$this->exception->getTraceAsString()}>
    </pre>

    <h3>参数:</h3>
    <pre>
        <{var_export($this->request)}>
        <!--{var_export($this->request->getParams(),true)}-->
    </pre>
        </div>
<{/if}>
</div>