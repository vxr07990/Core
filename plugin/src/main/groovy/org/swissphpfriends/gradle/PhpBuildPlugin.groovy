package org.swissphpfriends.gradle;

import org.gradle.api.*
import org.swissphpfriends.gradle.task.ComposerInstall
import org.swissphpfriends.gradle.task.PhpUnit;

class PhpBuildPlugin implements Plugin<Project> {

    void apply(Project project) {
        project.task('composerInstall', type: ComposerInstall)
        project.task('phpunit', type: PhpUnit)
    }
}