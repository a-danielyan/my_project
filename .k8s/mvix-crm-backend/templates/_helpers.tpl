{{/*
Expand the name of the chart.
*/}}
{{- define "mvix-crm-backend-php.name" -}}
{{- default "mvix-crm-backend-php" .Values.php.nameOverride | trunc 63 | trimSuffix "-" -}}
{{- end }}

{{- define "mvix-crm-backend-nginx.name" -}}
{{- default "mvix-crm-backend-nginx" .Values.nginx.nameOverride | trunc 63 | trimSuffix "-" }}
{{- end }}

{{- define "mvix-crm-backend-php.fullname" -}}
{{- default "mvix-crm-backend-php" .Values.php.fullnameOverride | trunc 63 | trimSuffix "-" -}}
{{- end }}

{{- define "mvix-crm-backend-nginx.fullname" -}}
{{- default "mvix-crm-backend-nginx" .Values.nginx.fullnameOverride | trunc 63 | trimSuffix "-" }}
{{- end }}


{{/*
Create chart name and version as used by the chart label.
*/}}
{{- define "mvix-crm-backend.chart" -}}
{{- printf "%s-%s" .Chart.Name .Chart.Version | replace "+" "_" | trunc 63 | trimSuffix "-" -}}
{{- end -}}

{{/*
Common labels
*/}}
{{- define "mvix-crm-backend-php.labels" -}}
helm.sh/chart: {{ include "mvix-crm-backend.chart" . }}
{{ include "mvix-crm-backend-php.selectorLabels" . }}
{{- if .Chart.AppVersion }}
app.kubernetes.io/version: {{ .Chart.AppVersion | quote }}
{{- end }}
app.kubernetes.io/managed-by: {{ .Release.Service }}
logs: php-fpm
exporter: php-fpm
{{- end }}

{{- define "mvix-crm-backend-nginx.labels" -}}
helm.sh/chart: {{ include "mvix-crm-backend.chart" . }}
{{ include "mvix-crm-backend-nginx.selectorLabels" . }}
{{- if .Chart.AppVersion }}
app.kubernetes.io/version: {{ .Chart.AppVersion | quote }}
{{- end }}
logs: nginx
{{- end }}

{{/*
Selector labels
*/}}
{{- define "mvix-crm-backend-php.selectorLabels" -}}
app.kubernetes.io/name: {{ include "mvix-crm-backend-php.name" . }}
app.kubernetes.io/instance: {{ .Release.Name }}
{{- end }}

{{- define "mvix-crm-backend-nginx.selectorLabels" -}}
app.kubernetes.io/name: {{ include "mvix-crm-backend-nginx.name" . }}
app.kubernetes.io/instance: {{ .Release.Name }}
{{- end }}

{{- define "scheduler.name" -}}
{{- default "scheduler" .Values.scheduler.nameOverride | trunc 63 | trimSuffix "-" -}}
{{- end -}}

{{/*
Common scheduler labels
*/}}
{{- define "scheduler.labels" -}}
{{ include "scheduler.selectorLabels" . }}
app.kubernetes.io/managed-by: {{ .Release.Service }}
env: {{ .Release.Namespace }}
logs: php-fpm
{{- end -}}

{{/*
scheduler Selector labels
*/}}
{{- define "scheduler.selectorLabels" -}}
app.kubernetes.io/name: {{ include "scheduler.name" . }}
app.kubernetes.io/instance: {{ .Release.Name }}
{{- end -}}
