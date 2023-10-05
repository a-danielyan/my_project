#How to build multi-arch docker image with Buildah:

##Run buildah in container from the folder where dockerfile located:

```sh
docker run -it --device /dev/fuse:rw --security-opt seccomp=unconfined --security-opt apparmor=unconfined -v $(pwd):/tmp/docker quay.io/buildah/stable:latest /bin/sh
```

##Build images

```sh
for i in arm64 amd64
do
    buildah bud --squash --manifest registry.gitlab.com/xhibitsignage-v3/xhibitsignage-v3-backend:nginx-1.23.1-alpine-lua --arch $i --build-arg ENABLED_MODULES="ndk lua" -f /tmp/docker/Dockerfile.production /tmp/docker
done
```

##Push images

```sh
buildah login registry.gitlab.com
buildah manifest push registry.gitlab.com/xhibitsignage-v3/xhibitsignage-v3-backend:nginx-1.23.1-alpine-lua docker://registry.gitlab.com/xhibitsignage-v3/xhibitsignage-v3-backend:nginx-1.23.1-alpine-lua --all --format v2s2
```
